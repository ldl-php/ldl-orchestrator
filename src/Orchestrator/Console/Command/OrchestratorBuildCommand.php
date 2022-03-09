<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Console\Command;

use LDL\DependencyInjection\CompilerPass\Compiler\CompilerPassCompiler;
use LDL\DependencyInjection\CompilerPass\Finder\CompilerPassFileFinder;
use LDL\DependencyInjection\CompilerPass\Finder\Options\CompilerPassFileFinderOptions;
use LDL\DependencyInjection\Container\Builder\LDLContainerBuilder;
use LDL\DependencyInjection\Service\Compiler\ServiceCompiler;
use LDL\DependencyInjection\Service\File\Finder\Options\ServiceFileFinderOptions;
use LDL\DependencyInjection\Service\File\Finder\ServiceFileFinder;
use LDL\Env\Builder\EnvBuilder;
use LDL\Env\File\Finder\EnvFileFinder;
use LDL\Env\File\Finder\Options\EnvFileFinderOptions;
use LDL\Env\Util\Compiler\EnvCompiler;
use LDL\Env\Util\File\Parser\EnvFileParser;
use LDL\File\Directory;
use LDL\File\Exception\FileExistsException;
use LDL\File\File;
use LDL\File\Helper\FilePathHelper;
use LDL\Framework\Base\Collection\CallableCollection;
use LDL\Orchestrator\Builder\OrchestratorBuilder;
use LDL\Orchestrator\Builder\OrchestratorBuilderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OrchestratorBuildCommand extends Command
{
    public const COMMAND_NAME = 'ldl:orchestrator:build';

    /**
     * @var OrchestratorBuilderInterface
     */
    private $orchestratorBuilder;

    public function __construct(
        ?string $name = null,
        OrchestratorBuilderInterface $orchestratorBuilder = null
    ) {
        parent::__construct($name ?? self::COMMAND_NAME);
        $this->orchestratorBuilder = $orchestratorBuilder;
    }

    public function configure(): void
    {
        parent::configure();

        $this->setDefinition(
            new InputDefinition([
                new InputArgument(
                    'output-directory',
                    InputArgument::REQUIRED
                ),
                new InputArgument(
                    'directories',
                    InputArgument::REQUIRED,
                    'Directories to search'
                ),
                new InputOption(
                    'dump-options',
                    'c',
                    InputOption::VALUE_REQUIRED,
                    'Path to JSON file containing container options'
                ),
                new InputOption(
                    'force',
                    'f',
                    InputOption::VALUE_OPTIONAL,
                    'Force creation (will destroy anything previously created)',
                    false
                ),
            ])
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $start = hrtime(true);

        $outputDirectory = $input->getArgument('output-directory');

        $pattern = sprintf('#^%s#', \DIRECTORY_SEPARATOR);

        $outputDirectory = preg_match($pattern, $outputDirectory) ? $outputDirectory : FilePathHelper::createAbsolutePath(getcwd(), $outputDirectory);

        $options = [];

        if ($input->getOption('dump-options')) {
            $options = new File($input->getOption('dump-options'));
            $options = json_decode($options->getLinesAsString(), true, 512, JSON_THROW_ON_ERROR);
        }

        try {
            $directory = Directory::create(
                $outputDirectory,
                0755,
                (bool) $input->getOption('force')
            );
        } catch (FileExistsException $e) {
            $output->writeln("<fg=red>{$e->getMessage()}, use -f option if you want to overwrite</>");

            return self::FAILURE;
        }

        $envFileFinder = new EnvFileFinder(
            EnvFileFinderOptions::fromArray([
                'directories' => explode(',', $input->getArgument('directories')),
            ]),
            new CallableCollection([
                static function ($file) use ($output) {
                    $output->writeln("Found env file $file ...");
                },
            ])
        );

        $serviceFileFinder = new ServiceFileFinder(
            ServiceFileFinderOptions::fromArray([
                'directories' => explode(',', $input->getArgument('directories')),
            ]),
            new CallableCollection([
                static function ($file) use ($output) {
                    $output->writeln("Found service file $file ...");
                },
            ])
        );

        $compilerPassFileFinder = new CompilerPassFileFinder(
            CompilerPassFileFinderOptions::fromArray([
                'directories' => explode(',', $input->getArgument('directories')),
            ]),
            new CallableCollection([
                static function ($file) use ($output) {
                    $output->writeln("Found compiler pass file $file ...");
                },
            ])
        );

        $envBuilder = new EnvBuilder(
            new EnvFileParser(null, null, null),
            new EnvCompiler()
        );

        $containerBuilder = new LDLContainerBuilder(
            new ServiceCompiler(),
            new CompilerPassCompiler()
        );

        $builder = new OrchestratorBuilder(
            $envFileFinder,
            $serviceFileFinder,
            $compilerPassFileFinder,
            $envBuilder,
            $containerBuilder,
            $options
        );

        $builder->build($directory)->write(
            $directory->mkpath('ldl-orchestrator-config.json'),
            (bool) $input->getOption('force')
        );

        $builder->getConfig()->write($directory->mkpath('ldl-orchestrator-build-config.json'));

        $end = hrtime(true);
        $total = round((($end - $start) / 1e+6) / 1000, 2);

        $output->writeln("\n<info>Took: $total seconds</info>");

        return self::SUCCESS;
    }
}

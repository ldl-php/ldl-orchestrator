<?php declare(strict_types=1);

namespace LDL\Orchestrator\Console\Command;

use LDL\Orchestrator\Builder\Config\Finder\Options\BuilderConfigFinderOptions;
use LDL\Orchestrator\Builder\Config\Reader\Options\BuilderConfigReaderOptions;
use LDL\Orchestrator\Builder\Config\Writer\Options\BuilderConfigWriterOptions;
use LDL\Orchestrator\Config\OrchestratorConfig;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use LDL\Orchestrator\Builder\Builder;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractOrchestratorCommand extends SymfonyCommand
{
    public const EXIT_SUCCESS = 0;
    public const EXIT_ERROR = 1;
    public const DEFAULT_MODE = OrchestratorConfig::DEFAULT_MODE;

    /**
     * @var Builder
     */
    protected $orchestrator;

    public function configure() : void
    {
        $builderConfigFinder = BuilderConfigFinderOptions::fromArray([]);
        $builderConfigWriter = BuilderConfigWriterOptions::fromArray([]);

        $this->addArgument(
            'output-file',
            InputArgument::OPTIONAL,
            'Name of the output file',
            $builderConfigWriter->getFile()
            )
            ->addOption(
                'input-file',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Specify input configuration file',
                $builderConfigWriter->getJsonFile()
            )
            ->addOption(
                'force-overwrite',
                'w',
                InputOption::VALUE_NONE,
                'Overwrite output file'
            )
            ->addOption(
                'scan-directories',
                'd',
                InputOption::VALUE_OPTIONAL,
                'Comma separated list of directories to scan',
                implode(',', $builderConfigFinder->getDirectories())
            )
            ->addOption(
                'excluded-directories',
                'e',
                InputOption::VALUE_OPTIONAL,
                'Comma separated list of excluded directories to scan'
            )
            ->addOption(
                'scan-file',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Name of config file to find',
                $builderConfigFinder->getFile()
            )
            ->addOption(
                'ignore-read-errors',
                'i',
                InputOption::VALUE_NONE,
                'Ignore syntax errors in service files'
            )
            ->addOption(
                'file-perms',
                'p',
                InputOption::VALUE_OPTIONAL,
                'File permission',
                $builderConfigWriter->getFilePerms()
            )
            ->addOption(
                'is-dev-mode',
                's',
                InputOption::VALUE_NONE,
                'Specify mode: ENV or PROD'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {

            $this->build($input, $output);
            return self::EXIT_SUCCESS;

        }catch(\Exception $e){

            $output->writeln("<error>{$e->getMessage()}</error>");
            return self::EXIT_ERROR;

        }
    }

    private function build(InputInterface $input, OutputInterface $output)
    {
        $excludedDirectories = $input->getOption('excluded-directories');

        $finderOptions = BuilderConfigFinderOptions::fromArray([
            'directories' => explode(',', $input->getOption('scan-directories')),
            'file' => $input->getOption('scan-file'),
            'excludedDirectories' => null !== $excludedDirectories ? explode(',', $excludedDirectories) : []
        ]);

        $readerOptions = BuilderConfigReaderOptions::fromArray([
            'ignoreErrors' => $input->getOption('ignore-read-errors')
        ]);

        $writerOptions = BuilderConfigWriterOptions::fromArray([
            'file' => $input->getArgument('output-file'),
            'filePerms' => $input->getOption('file-perms'),
            'force' => $input->getOption('force-overwrite')
        ]);

        $orchestratorConfig = OrchestratorConfig::fromArray([
            'source' => $input->getArgument('output-file') ?? getcwd(),
            'mode' => false === $input->getOption('is-dev-mode') ? 'PROD' : 'DEV',
            'config' => [
                'finder' => $finderOptions->toArray(),
                'reader' => $readerOptions->toArray(),
                'writer' => $writerOptions->toArray()
            ]
        ]);

        $this->orchestrator = new Builder(
            $input->getOption('is-dev-mode'),
            null,
            null,
            $orchestratorConfig
        );
    }
}
<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Console\Command;

use LDL\Orchestrator\Builder\Config\Finder\BuilderConfigFinder;
use LDL\Orchestrator\Builder\Config\Finder\Options\BuilderConfigFinderOptions;
use LDL\Orchestrator\Builder\Config\Reader\BuilderConfigReader;
use LDL\Orchestrator\Builder\Config\Reader\Options\BuilderConfigReaderOptions;
use LDL\Orchestrator\Builder\Config\Writer\BuilderConfigWriter;
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

    /**
     * @var Builder
     */
    protected $orchestrator;

    public function configure() : void
    {
        $builderFinder = BuilderConfigFinderOptions::fromArray([]);
        $builderWriter = BuilderConfigWriterOptions::fromArray([]);

        $this->addArgument(
            'output-file',
            InputArgument::OPTIONAL,
            'Name of the output file',
            $builderWriter->getFile()
            )
            ->addOption(
                'input-file',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Specify input configuration file'
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
                $builderFinder->getDirectories()
            )
            ->addOption(
                'excluded-directories',
                'e',
                InputOption::VALUE_OPTIONAL,
                'Comma separated list of excluded directories to scan',
                $builderFinder->getExcludedDirectories()
            )
            ->addOption(
                'scan-file',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Name of config file to find',
                $builderFinder->getFile()
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
                $builderWriter->getFilePerms()
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
        if(null !== $input->getOption('input-file')){
            $this->orchestrator = Builder::fromConfigFile($input->getOption('input-file'));
            return;
        }

        $finderOptions = BuilderConfigFinderOptions::fromArray([
            'directories' => explode(',', $input->getOption('scan-directories')),
            'file' => $input->getOption('scan-file'),
            'excludedDirectories' => $input->getOption('excluded-directories')
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
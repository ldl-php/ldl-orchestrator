<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Console\Command;

use LDL\Orchestrator\Builder\OrchestratorBuilder;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractOrchestratorCommand extends SymfonyCommand
{
    public const EXIT_SUCCESS = 0;
    public const EXIT_ERROR = 1;

    /**
     * @var Orchestrator
     */
    protected $orchestrator;

    protected const DEFAULT_SCAN_DIRECTORIES = 'framework, application, plugin';
    protected const DEFAULT_SCAN_FILES = 'services.xml, guards.xml, events.xml, commands.xml, loggers.xml, controllers.xml';

    public function configure(): void
    {
        $cwd = getcwd();

        $this->addArgument(
            'project-dir',
            InputArgument::OPTIONAL,
            "Specify project directory, default \"$cwd\"",
            $cwd
        )
            ->addOption(
                'scan-directories',
                'd',
                InputOption::VALUE_REQUIRED,
                sprintf(
                    'Comma separated list of directories to scan, default: %s',
                    self::DEFAULT_SCAN_DIRECTORIES
                ),
                self::DEFAULT_SCAN_DIRECTORIES
            )
            ->addOption(
                'scan-files',
                'l',
                InputOption::VALUE_REQUIRED,
                sprintf(
                    'Comma separated list of files to scan, default: %s',
                    self::DEFAULT_SCAN_FILES
                ),
                self::DEFAULT_SCAN_FILES
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->orchestrator = OrchestratorBuilder::factory(
            $input->getArgument('project-dir'),
            array_map('trim', explode(',', $input->getOption('scan-directories'))),
            array_map('trim', explode(',', $input->getOption('scan-files')))
        );
    }
}

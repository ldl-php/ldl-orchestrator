<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Console\Command;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Dumper\GraphvizDumper;

class OrchestratorGraphVizCommand extends AbstractOrchestratorCommand
{
    public const COMMAND_NAME = 'graph:dump';

    public function configure(): void
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('Generate a comprehensive graphic of service dependencies')
            ->addArgument(
            'output',
            InputArgument::REQUIRED,
            'Output file'
            )
            ->addOption(
                'dev-mode',
                'm',
                InputOption::VALUE_REQUIRED,
                'Generate graph for container in development or production mode',
                'prod'
            );

            parent::configure();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            parent::execute($input, $output);

            $isDevelopment = in_array(
                $input->getOption('dev-mode'),
                [
                    'dev',
                    'development',
                    'develop'
                ],
                true
            );

            $title = sprintf('[ Building container in "%s" mode ]', $isDevelopment ? 'DEV' : 'PROD');

            $output->writeln("\n<info>$title</info>\n");

            $progressBar = new ProgressBar($output);
            $progressBar->start();

            $dumper = new GraphvizDumper(
                $this->orchestrator->compile(
                        $isDevelopment,
                        'ldl.xml',
                        $progressBar
                )
            );

            $progressBar->finish();

            $output->writeln("\n");

            $outFile = $input->getArgument('output');

            file_put_contents($outFile, $dumper->dump());

            $msg = "You need to run the \"dot\" utility from graphviz in order to render the graph as an image";
            $output->writeln("<fg=yellow>$msg</>");

            $msg = "For example: dot -Tpng $outFile > $outFile.png";
            $output->writeln("\n<fg=yellow>$msg</>\n");
            $output->writeln("<info>See https://graphviz.org/download/ to get the graphviz tool</info>\n");

            return parent::EXIT_SUCCESS;
        }catch(\Exception $e){
            $output->writeln("<error>{$e->getMessage()}</error>");
            return parent::EXIT_ERROR;
        }

    }

}
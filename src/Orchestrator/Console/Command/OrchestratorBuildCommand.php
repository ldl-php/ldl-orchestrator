<?php

namespace LDL\Orchestrator\Console\Command;

use LDL\FsUtil\util\Fs;
use LDL\Orchestrator\Builder\Exception\OrchestratorContainerExistsException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OrchestratorBuildCommand extends AbstractOrchestratorCommand
{
    public const COMMAND_NAME = 'build';

    public function configure() : void
    {
        parent::configure();

        $cwd = getcwd();
        $outputFile = Fs::mkPath($cwd,'cache', 'container.php');

        $this->setName(self::COMMAND_NAME)
            ->setDescription('Builds container dependencies')
            ->addArgument(
                'output-file',
                InputArgument::OPTIONAL,
                "Specify output file, default: \"$outputFile\"",
                $outputFile
            )
            ->addOption(
                'dev-mode',
                'm',
                InputOption::VALUE_OPTIONAL,
                'Build container in for development or production',
                'prod'
            )
            ->addOption(
                'force-rebuild',
                'f',
                InputOption::VALUE_NONE,
                'Rebuilds the container even if a cached container is found'
            )
            ->addOption(
                'file-permissions',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Sets file permissions on generated container file',
                '0666'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            parent::execute($input, $output);

            $this->build($input, $output);

            return parent::EXIT_SUCCESS;

        }catch(\Exception $e){
            $output->writeln("<error>{$e->getMessage()}</error>");
            return parent::EXIT_ERROR;
        }
    }

    private function build(
        InputInterface $input,
        OutputInterface $output
    ) : void
    {
        $start = hrtime(true);

        try {

            $isDevelopment = in_array(
                $input->getOption('dev-mode'),
                [
                    'd',
                    'dev',
                    'development',
                    'develop'
                ],
                true
            );

            $title = sprintf('[ Building container in "%s" mode ]', $isDevelopment ? 'DEV' : 'PROD');

            $output->writeln("\n<info>$title</info>\n");

            $progressBar = ProgressBarFactory::build($output);
            $progressBar->start();

            $this->orchestrator->write(
                $this->orchestrator->compile(
                    $isDevelopment,
                    'ldl.xml',
                    $progressBar
                ),
                $input->getArgument('output-file'),
                intval($input->getOption('file-permissions'), 8),
                (bool)$input->getOption('force-rebuild')
            );

            $output->writeln("");

        }catch(OrchestratorContainerExistsException $e){

            $output->writeln("\n<error>{$e->getMessage()}</error>\n");
            $output->writeln('<info>If this is wanted, please use the force-rebuild option</info>');

        }catch(\Exception $e){

            $output->writeln("\n<error>Build failed!</error>\n");
            $output->writeln("\n<error>{$e->getMessage()}</error>\n");

            return;
        }

        $progressBar->finish();

        $end = hrtime(true);
        $total = round((($end - $start) / 1e+6) / 1000,2);

        $output->writeln("\n<info>Took: $total seconds</info>");
    }

    public function getOrchestrator() : ?Orchestrator
    {
        return $this->orchestrator;
    }

}
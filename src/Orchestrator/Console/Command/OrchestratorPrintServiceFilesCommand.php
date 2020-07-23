<?php

namespace LDL\Orchestrator\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo as FileInfo;

class OrchestratorPrintServiceFilesCommand extends AbstractOrchestratorCommand
{
    public const COMMAND_NAME = 'service:print';

    public function configure() : void
    {
        parent::configure();

        $this->setName(self::COMMAND_NAME)
            ->setDescription('Prints service files');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            parent::execute($input, $output);

            $this->printServiceFiles($input, $output);

            return parent::EXIT_SUCCESS;

        }catch(\Exception $e){
            $output->writeln("<error>{$e->getMessage()}</error>");
            return parent::EXIT_ERROR;
        }
    }

    private function printServiceFiles(
        InputInterface $input,
        OutputInterface $output
    ) : void
    {
        $total = 0;

        $output->writeln("<info>[ Service files list ]</info>\n");

        /**
         * @var FileInfo $file
         */
        foreach($this->orchestrator->findServiceFiles() as $file){
            $total++;
            $output->writeln($file->getRealPath());
        }

        $output->writeln("\n<info>Total files: $total</info>");
    }

}
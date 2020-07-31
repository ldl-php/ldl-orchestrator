<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Console\Command;

use LDL\DependencyInjection\CompilerPass\Finder\Exception\NoFilesFoundException;
use LDL\Orchestrator\Builder\Builder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo as FileInfo;

class OrchestratorPrintCompilerPassesCommand extends AbstractOrchestratorCommand
{
    public const COMMAND_NAME = 'cpass:print';

    public function configure() : void
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('Print available compiler pass files');
        parent::configure();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            parent::execute($input, $output);

            $this->printCompilerPasses($input, $output);

            return parent::EXIT_SUCCESS;

        }catch(\Exception $e){
            $output->writeln("<error>{$e->getMessage()}</error>");
            return parent::EXIT_ERROR;
        }
    }

    private function printCompilerPasses(
        InputInterface $input,
        OutputInterface $output
    ) : void
    {
        $total = 0;
        $output->writeln("<info>[ Compiler pass file list ]</info>\n");

        try{
            $files = $this->orchestrator->getLDLContainerBuilder()->getCompilerPassFinder()->find();
        }catch(NoFilesFoundException $e){
            $output->writeln("\n<error>{$e->getMessage()}</error>\n");

            return;
        }


        /**
         * @var FileInfo $file
         */
        foreach($files as $file){
            $total++;
            $output->writeln($file->getRealPath());
        }

        $output->writeln("\n<info>Total files: $total</info>");
    }

    public function getOrchestrator() : ?Builder
    {
        return $this->orchestrator;
    }

}
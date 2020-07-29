<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo as FileInfo;

class OrchestratorPrintCompilerPassesCommand extends AbstractOrchestratorCommand
{
    public const COMMAND_NAME = 'pass:print';

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

        /**
         * @var FileInfo $compilerPass
         */
        foreach($this->orchestrator->findCompilerPasses() as $compilerPass){
            $total++;
            $output->writeln($compilerPass->getRealPath());
        }

        $output->writeln("\n<info>Total compiler passes: $total</info>");
    }

    public function getOrchestrator() : ?Orchestrator
    {
        return $this->orchestrator;
    }

}
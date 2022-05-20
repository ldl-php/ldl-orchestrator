<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Dumper;

use LDL\File\Contracts\DirectoryInterface;
use LDL\Orchestrator\Compiler\CompiledOrchestratorInterface;

interface OrchestratorDumperInterface
{
    /**
     * Dumps a compiled orchestrator into a directory.
     */
    public function dump(CompiledOrchestratorInterface $compiled, DirectoryInterface $output): void;
}

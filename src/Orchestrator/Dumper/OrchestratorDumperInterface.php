<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Dumper;

use LDL\File\Contracts\DirectoryInterface;
use LDL\Orchestrator\Builder\BuiltOrchestratorInterface;

interface OrchestratorDumperInterface
{
    /**
     * Dumps a compiled orchestrator into a directory.
     */
    public function dump(BuiltOrchestratorInterface $compiled, DirectoryInterface $output): void;
}

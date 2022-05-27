<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Compiler;

use LDL\Env\Util\File\Exception\ReadEnvFileException;

interface OrchestratorCompilerInterface
{
    /**
     * Takes in several orchestrators and combines them into one final CompiledOrchestrator.
     *
     * @throws ReadEnvFileException
     */
    public function compile(): CompiledOrchestratorInterface;
}

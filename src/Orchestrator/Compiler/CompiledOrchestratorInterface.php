<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Compiler;

use LDL\Env\Util\Line\Collection\EnvLineCollectionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

interface CompiledOrchestratorInterface
{
    public function getContainer(): ContainerBuilder;

    public function getEnvLines(): EnvLineCollectionInterface;
}

<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder;

use LDL\Env\Util\Line\Collection\EnvLineCollectionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

interface BuiltOrchestratorInterface
{
    public function getContainer(): ContainerBuilder;

    public function getEnvLines(): EnvLineCollectionInterface;
}

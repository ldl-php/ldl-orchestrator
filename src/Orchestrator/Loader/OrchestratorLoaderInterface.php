<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Loader;

use LDL\Orchestrator\Config\OrchestratorConfigInterface;
use Psr\Container\ContainerInterface;

interface OrchestratorLoaderInterface
{
    public static function fromJSONFile(string $file): OrchestratorLoaderInterface;

    public function getContainer(): ContainerInterface;

    public function getConfig(): OrchestratorConfigInterface;
}

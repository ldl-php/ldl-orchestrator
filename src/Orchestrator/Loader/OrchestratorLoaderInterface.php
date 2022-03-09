<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Loader;

use LDL\Framework\Base\Contracts\ArrayFactoryInterface;
use LDL\Framework\Base\Contracts\JsonFactoryInterface;
use LDL\Framework\Base\Contracts\JsonFileFactoryInterface;
use LDL\Orchestrator\Config\OrchestratorConfigInterface;
use Psr\Container\ContainerInterface;

interface OrchestratorLoaderInterface extends JsonFileFactoryInterface, JsonFactoryInterface, ArrayFactoryInterface
{
    public function getContainer(): ContainerInterface;

    public function getConfig(): OrchestratorConfigInterface;
}

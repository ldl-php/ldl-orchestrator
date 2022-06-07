<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder;

use LDL\Env\Util\Line\Collection\EnvLineCollectionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BuiltOrchestrator implements BuiltOrchestratorInterface
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var EnvLineCollectionInterface
     */
    private $envLines;

    public function __construct(
        ContainerBuilder $container,
        EnvLineCollectionInterface $envLines
    ) {
        $this->container = $container;
        $this->envLines = $envLines;
    }

    public function getContainer(): ContainerBuilder
    {
        return $this->container;
    }

    public function getEnvLines(): EnvLineCollectionInterface
    {
        return $this->envLines;
    }
}

<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder;

use LDL\Env\Util\File\Exception\ReadEnvFileException;
use Symfony\Component\DependencyInjection\ContainerInterface;

interface OrchestratorBuilderInterface
{
    /**
     * Takes in several orchestrators and combines them into one final BuiltOrchestrator.
     *
     * @throws ReadEnvFileException
     */
    public function compile(?ContainerInterface $container): BuiltOrchestratorInterface;
}

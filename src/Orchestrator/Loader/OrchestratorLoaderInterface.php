<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Loader;

use LDL\File\Contracts\DirectoryInterface;
use LDL\Orchestrator\Builder\BuiltOrchestratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

interface OrchestratorLoaderInterface
{
    /**
     * Loads a compiled orchestrator in memory, useful when dependencies are not yet set in stone
     * (when you are developing).
     *
     * NOTE: This method must never be used on a productive environment, use self::loadDirectory instead
     */
    public function load(BuiltOrchestratorInterface $compiledOrchestrator): ContainerInterface;

    /**
     * Loads a compiled orchestrator from files.
     */
    public function loadDirectory(DirectoryInterface $directory): ContainerInterface;
}

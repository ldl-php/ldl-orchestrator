<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Facade;

use LDL\Env\Util\File\Exception\ReadEnvFileException;
use LDL\File\Collection\Contracts\DirectoryCollectionInterface;
use LDL\Framework\Base\Collection\CallableCollectionInterface;
use LDL\Orchestrator\Builder\BuiltOrchestratorInterface;
use LDL\Orchestrator\Orchestrator;
use LDL\Orchestrator\OrchestratorInterface;
use LDL\Type\Collection\Interfaces\Type\StringCollectionInterface;

interface OrchestratorFacadeInterface
{
    /**
     * Finds all $serviceFiles (and $envFiles) in given $directories by traversing said directories without too much
     * hassle.
     *
     * NOTE: This is an opinionated implementation of the orchestrator build process, if you want to modify this
     * process, create your own facade/factory.
     */
    public static function fromDirectory(
        DirectoryCollectionInterface $directories,
        StringCollectionInterface $serviceFiles,
        CallableCollectionInterface $onEnvFileFound = null,
        CallableCollectionInterface $onServiceFileFound = null,
        CallableCollectionInterface $onCpassFileFound = null
    ): OrchestratorInterface;

    /**
     * Builds an orchestrator straight from service files, this can be used when you intend to build
     * from specific files, without any kind of directory traversing / finding.
     *
     * NOTE: This is an opinionated implementation of the orchestrator build process, if you want to modify this
     * process, create your own facade/factory.
     *
     * @throws ReadEnvFileException
     */
    public static function fromFiles(
        iterable $serviceFiles,
        iterable $envFiles = null,
        iterable $compilerPassFiles = null
    ): BuiltOrchestratorInterface;
}

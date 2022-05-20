<?php

declare(strict_types=1);

namespace LDL\Orchestrator;

use LDL\DependencyInjection\CompilerPass\Finder\CompilerPassFileFinderInterface;
use LDL\DependencyInjection\Container\Builder\LDLContainerBuilderInterface;
use LDL\DependencyInjection\Service\File\Finder\ServiceFileFinderInterface;
use LDL\Env\Builder\EnvBuilderInterface;
use LDL\Env\File\Finder\EnvFileFinderInterface;

interface OrchestratorInterface
{
    public function getEnvFinder(): EnvFileFinderInterface;

    public function getEnvBuilder(): EnvBuilderInterface;

    public function getServiceFinder(): ServiceFileFinderInterface;

    public function getContainerBuilder(): LDLContainerBuilderInterface;

    public function getCompilerPassFinder(): CompilerPassFileFinderInterface;
}

<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Facade;

use LDL\DependencyInjection\CompilerPass\Compiler\CompilerPassCompiler;
use LDL\DependencyInjection\CompilerPass\Finder\CompilerPassFileFinder;
use LDL\DependencyInjection\CompilerPass\Finder\Options\CompilerPassFileFinderOptions;
use LDL\DependencyInjection\Container\Builder\LDLContainerBuilder;
use LDL\DependencyInjection\Service\Compiler\ServiceCompiler;
use LDL\DependencyInjection\Service\File\Finder\Options\ServiceFileFinderOptions;
use LDL\DependencyInjection\Service\File\Finder\ServiceFileFinder;
use LDL\Env\Builder\EnvBuilder;
use LDL\Env\File\Finder\EnvFileFinder;
use LDL\Env\File\Finder\Options\EnvFileFinderOptions;
use LDL\Env\Util\Compiler\EnvCompiler;
use LDL\Env\Util\File\Parser\EnvFileParser;
use LDL\File\Collection\Contracts\DirectoryCollectionInterface;
use LDL\Framework\Base\Collection\CallableCollectionInterface;
use LDL\Orchestrator\Orchestrator;
use LDL\Orchestrator\OrchestratorInterface;
use LDL\Type\Collection\Interfaces\Type\StringCollectionInterface;

class OrchestratorFacade
{
    public static function fromDirectory(
        DirectoryCollectionInterface $directories,
        StringCollectionInterface $serviceFiles,
        CallableCollectionInterface $onEnvFileFound = null,
        CallableCollectionInterface $onServiceFileFound = null,
        CallableCollectionInterface $onCpassFileFound = null
    ): OrchestratorInterface {
        $envFileFinder = new EnvFileFinder(
            new EnvFileFinderOptions($directories),
            $onEnvFileFound
        );

        $serviceFileFinder = new ServiceFileFinder(
            new ServiceFileFinderOptions($directories, $serviceFiles),
            $onServiceFileFound
        );

        $cpassFileFinder = new CompilerPassFileFinder(
            new CompilerPassFileFinderOptions($directories),
            $onCpassFileFound
        );

        $envBuilder = new EnvBuilder(
            new EnvFileParser(null, null, null),
            new EnvCompiler()
        );

        $containerBuilder = new LDLContainerBuilder(
            new ServiceCompiler(),
            new CompilerPassCompiler()
        );

        return new Orchestrator(
            $envFileFinder,
            $serviceFileFinder,
            $cpassFileFinder,
            $envBuilder,
            $containerBuilder
        );
    }
}

<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder\Factory;

use LDL\DependencyInjection\CompilerPass\Compiler\CompilerPassCompiler;
use LDL\DependencyInjection\CompilerPass\Finder\CompilerPassFileFinder;
use LDL\DependencyInjection\CompilerPass\Finder\Options\CompilerPassFileFinderOptions;
use LDL\DependencyInjection\Container\Builder\LDLContainerBuilder;
use LDL\DependencyInjection\Service\Compiler\ServiceCompiler;
use LDL\DependencyInjection\Service\File\Finder\Options\ServiceFileFinderOptions;
use LDL\DependencyInjection\Service\File\Finder\ServiceFileFinder;
use LDL\Env\Builder\EnvBuilder;
use LDL\Env\File\Finder\EnvFileFinder;
use LDL\Env\File\Finder\Options\EnvFileFinderOptionsInterface;
use LDL\Env\Util\Compiler\EnvCompiler;
use LDL\Env\Util\File\Parser\EnvFileParser;
use LDL\Orchestrator\Builder\OrchestratorBuilder;
use LDL\Orchestrator\Builder\OrchestratorBuilderInterface;

class OrchestratorBuilderFactory
{
    public static function create(
        EnvFileFinderOptionsInterface $envFileFinderOptions,
        ServiceFileFinderOptions $serviceFileFinderOptions,
        CompilerPassFileFinderOptions $compilerPassFileFinderOptions
    ): OrchestratorBuilderInterface {
        $envFileFinder = new EnvFileFinder($envFileFinderOptions);
        $envCompiler = new EnvCompiler();
        $envFileParser = new EnvFileParser(null, null, null);

        $serviceFileFinder = new ServiceFileFinder($serviceFileFinderOptions);
        $compilerPassFileFinder = new CompilerPassFileFinder($compilerPassFileFinderOptions);
        $serviceCompiler = new ServiceCompiler();
        $compilerPassCompiler = new CompilerPassCompiler();

        $envBuilder = new EnvBuilder($envFileParser, $envCompiler);
        $containerBuilder = new LDLContainerBuilder($serviceCompiler, $compilerPassCompiler);

        return new OrchestratorBuilder(
            $envFileFinder,
            $serviceFileFinder,
            $compilerPassFileFinder,
            $envBuilder,
            $containerBuilder
        );
    }
}

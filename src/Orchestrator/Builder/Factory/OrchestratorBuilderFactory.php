<?php
/**
 * Contains different methods to create an OrchestratorBuilder.
 */

declare(strict_types=1);

namespace LDL\Orchestrator\Builder\Factory;

use LDL\DependencyInjection\CompilerPass\Compiler\CompilerPassCompiler;
use LDL\DependencyInjection\CompilerPass\Finder\CompilerPassFileFinder;
use LDL\DependencyInjection\CompilerPass\Finder\Options\CompilerPassFileFinderOptionsInterface;
use LDL\DependencyInjection\Container\Builder\LDLContainerBuilder;
use LDL\DependencyInjection\Container\Options\ContainerDumpOptions;
use LDL\DependencyInjection\Container\Options\ContainerDumpOptionsInterface;
use LDL\DependencyInjection\Service\Compiler\ServiceCompiler;
use LDL\DependencyInjection\Service\File\Finder\Options\ServiceFileFinderOptionsInterface;
use LDL\DependencyInjection\Service\File\Finder\ServiceFileFinder;
use LDL\Env\Builder\EnvBuilder;
use LDL\Env\File\Finder\EnvFileFinder;
use LDL\Env\File\Finder\Options\EnvFileFinderOptionsInterface;
use LDL\Env\Util\Compiler\EnvCompiler;
use LDL\Env\Util\File\Parser\EnvFileParser;
use LDL\Orchestrator\Builder\Config\OrchestratorBuilderConfig;
use LDL\Orchestrator\Builder\Config\OrchestratorBuilderConfigInterface;
use LDL\Orchestrator\Builder\Factory\Exception\OrchestratorBuilderFactoryException;
use LDL\Orchestrator\Builder\OrchestratorBuilder;
use LDL\Orchestrator\Builder\OrchestratorBuilderInterface;

class OrchestratorBuilderFactory implements OrchestratorBuilderFactoryInterface
{
    public static function create(
        EnvFileFinderOptionsInterface $envFileFinderOptions,
        ServiceFileFinderOptionsInterface $serviceFileFinderOptions,
        CompilerPassFileFinderOptionsInterface $compilerPassFileFinderOptions,
        ContainerDumpOptionsInterface $dumpOptions = null
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
            $containerBuilder,
            $dumpOptions ?? new ContainerDumpOptions()
        );
    }

    public static function fromJsonFile(string $file): OrchestratorBuilderInterface
    {
        try {
            return self::fromConfig(OrchestratorBuilderConfig::fromJsonFile($file));
        } catch (\Throwable $e) {
            $msg = sprintf('Unable to create OrchestratorBuilder from file %s', $file);
            throw new OrchestratorBuilderFactoryException($msg, 0, $e);
        }
    }

    public static function fromJsonString(string $json)
    {
        try {
            return self::fromConfig(OrchestratorBuilderConfig::fromJsonString($json));
        } catch (\Throwable $e) {
            $msg = 'Unable to create OrchestratorBuilder from JSON string';
            throw new OrchestratorBuilderFactoryException($msg, 0, $e);
        }
    }

    public static function fromConfig(OrchestratorBuilderConfigInterface $config): OrchestratorBuilderInterface
    {
        try {
            return self::create(
                $config->getEnvFileFinderOptions(),
                $config->getServiceFileFinderOptions(),
                $config->getCompilerPassFileFinderOptions(),
                $config->getDumpOptions()
            );
        } catch (\Throwable $e) {
            throw new OrchestratorBuilderFactoryException('Could not create orchestrator builder from config', 0, $e);
        }
    }
}

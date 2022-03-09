<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder\Factory;

use LDL\DependencyInjection\CompilerPass\Finder\Options\CompilerPassFileFinderOptionsInterface;
use LDL\DependencyInjection\Service\File\Finder\Options\ServiceFileFinderOptionsInterface;
use LDL\Env\File\Finder\Options\EnvFileFinderOptionsInterface;
use LDL\Framework\Base\Contracts\JsonFactoryInterface;
use LDL\Framework\Base\Contracts\JsonFileFactoryInterface;
use LDL\Orchestrator\Builder\Config\OrchestratorBuilderConfigInterface;
use LDL\Orchestrator\Builder\Factory\Exception\OrchestratorBuilderFactoryException;
use LDL\Orchestrator\Builder\OrchestratorBuilderInterface;

interface OrchestratorBuilderFactoryInterface extends JsonFileFactoryInterface, JsonFactoryInterface
{
    public static function create(
        EnvFileFinderOptionsInterface $envFileFinderOptions,
        ServiceFileFinderOptionsInterface $serviceFileFinderOptions,
        CompilerPassFileFinderOptionsInterface $compilerPassFileFinderOptions
    ): OrchestratorBuilderInterface;

    /**
     * @throws OrchestratorBuilderFactoryException
     */
    public static function fromConfig(OrchestratorBuilderConfigInterface $config): OrchestratorBuilderInterface;
}

<?php

declare(strict_types=1);

namespace LDL\Orchestrator;

use LDL\DependencyInjection\CompilerPass\Finder\CompilerPassFileFinderInterface;
use LDL\DependencyInjection\Container\Builder\LDLContainerBuilderInterface;
use LDL\DependencyInjection\Service\File\Finder\ServiceFileFinderInterface;
use LDL\Env\Builder\EnvBuilderInterface;
use LDL\Env\File\Finder\EnvFileFinderInterface;
use LDL\Env\Util\Line\Collection\EnvLineCollectionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class Orchestrator implements OrchestratorInterface
{
    public const DEFAULT_CONTAINER_FILE_NAME = 'ldl-orchestrator.php';
    public const DEFAULT_ENV_FILE_NAME = 'ldl-env-compiled.env';

    /**
     * @var EnvFileFinderInterface
     */
    private $envFileFinder;

    /**
     * @var ServiceFileFinderInterface
     */
    private $serviceFileFinder;

    /**
     * @var EnvBuilderInterface
     */
    private $envBuilder;

    /**
     * @var LDLContainerBuilderInterface
     */
    private $containerBuilder;

    /**
     * @var CompilerPassFileFinderInterface
     */
    private $cPassFileFinder;

    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var EnvLineCollectionInterface
     */
    private $env;

    public function __construct(
        EnvFileFinderInterface $envFileFinder,
        ServiceFileFinderInterface $serviceFileFinder,
        CompilerPassFileFinderInterface $cPassFileFinder,
        EnvBuilderInterface $envBuilder,
        LDLContainerBuilderInterface $containerBuilder
    ) {
        $this->envFileFinder = $envFileFinder;
        $this->serviceFileFinder = $serviceFileFinder;
        $this->cPassFileFinder = $cPassFileFinder;
        $this->envBuilder = $envBuilder;
        $this->containerBuilder = $containerBuilder;
    }

    public function getServiceFinder(): ServiceFileFinderInterface
    {
        return $this->serviceFileFinder;
    }

    public function getContainerBuilder(): LDLContainerBuilderInterface
    {
        return $this->containerBuilder;
    }

    public function getCompilerPassFinder(): CompilerPassFileFinderInterface
    {
        return $this->cPassFileFinder;
    }

    public function getEnvFinder(): EnvFileFinderInterface
    {
        return $this->envFileFinder;
    }

    public function getEnvBuilder(): EnvBuilderInterface
    {
        return $this->envBuilder;
    }
}

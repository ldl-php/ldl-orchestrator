<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder;

use LDL\DependencyInjection\CompilerPass\Finder\CompilerPassFinder;
use LDL\DependencyInjection\CompilerPass\Finder\Options\CompilerPassFinderOptions;
use LDL\DependencyInjection\CompilerPass\Reader\CompilerPassReader;
use LDL\DependencyInjection\CompilerPass\Reader\Options\CompilerPassReaderOptions;
use LDL\DependencyInjection\Container\Builder\LDLContainerBuilderInterface;
use LDL\DependencyInjection\Container\Config\ContainerConfig;
use LDL\DependencyInjection\Service\Compiler\Options\ServiceCompilerOptions;
use LDL\DependencyInjection\Service\Compiler\ServiceCompiler;
use LDL\DependencyInjection\Service\Finder\Options\ServiceFileFinderOptions;
use LDL\DependencyInjection\Service\Finder\ServiceFileFinder;
use LDL\DependencyInjection\Service\Reader\Options\ServiceReaderOptions;
use LDL\DependencyInjection\Service\Reader\ServiceFileReader;
use LDL\Env\Builder\EnvBuilder;
use LDL\Env\Builder\EnvBuilderInterface;
use LDL\Env\Compiler\EnvCompiler;
use LDL\Env\Compiler\Options\EnvCompilerOptions;
use LDL\Env\Config\EnvConfig;
use LDL\Env\Finder\EnvFileFinder;
use LDL\Env\Finder\Options\EnvFileFinderOptions;
use LDL\DependencyInjection\Container\Builder\LDLContainerBuilder;
use LDL\FS\Type\Types\Generic\Collection\GenericFileCollection;
use LDL\Orchestrator\Builder\Config\Compiler\BuilderConfigCompiler;
use LDL\Orchestrator\Builder\Config\Compiler\BuilderConfigCompilerInterface;
use LDL\Orchestrator\Builder\Config\Config\BuilderConfig;
use LDL\Orchestrator\Builder\Config\Finder\BuilderConfigFinder;
use LDL\Orchestrator\Builder\Config\Finder\BuilderConfigFinderInterface;
use LDL\Orchestrator\Builder\Config\Finder\Exception\NoFileFoundException;
use LDL\Orchestrator\Builder\Config\Finder\Options\BuilderConfigFinderOptions;
use LDL\Orchestrator\Builder\Config\Reader\BuilderConfigReader;
use LDL\Orchestrator\Builder\Config\Reader\BuilderConfigReaderInterface;
use LDL\Orchestrator\Builder\Config\Reader\Options\BuilderConfigReaderOptions;
use LDL\Orchestrator\Builder\Config\Writer\BuilderConfigWriter;
use LDL\Orchestrator\Builder\Config\Writer\BuilderConfigWriterInterface;
use LDL\Orchestrator\Builder\Config\Writer\Options\BuilderConfigWriterOptions;
use LDL\Orchestrator\Config\OrchestratorConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Builder
{
    private const ENV_PRODUCTION_NAME = 'PROD';

    /**
     * @var OrchestratorConfig
     */
    private $config;

    /**
     * @var ContainerConfig
     */
    private $containerConfig;

    /**
     * @var EnvBuilderInterface
     */
    private $envBuilder;

    /**
     * @var LDLContainerBuilderInterface
     */
    private $LDLContainerBuilder;

    /**
     * @var BuilderConfigFinderInterface
     */
    private $configFinder;

    /**
     * @var BuilderConfigCompilerInterface
     */
    private $configCompiler;

    /**
     * @var BuilderConfigReaderInterface
     */
    private $configReader;

    /**
     * @var BuilderConfigWriterInterface
     */
    private $configWriter;

    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var bool
     */
    private $devMode;

    public function __construct(
        bool $devMode = false,
        EnvBuilderInterface $envBuilder = null,
        LDLContainerBuilderInterface $containerBuilder = null,
        OrchestratorConfig $orchestratorConfig = null
    )
    {
        $this->envBuilder = $envBuilder ?? new EnvBuilder();
        $this->LDLContainerBuilder = $containerBuilder ?? new LDLContainerBuilder();
        $this->config = $orchestratorConfig ?? new OrchestratorConfig();

        $this->configFinder = new BuilderConfigFinder(
            BuilderConfigFinderOptions::fromArray($this->config->getFinder())
        );

        $this->configReader = new BuilderConfigReader(
            BuilderConfigReaderOptions::fromArray($this->config->getReader())
        );

        $this->configWriter = new BuilderConfigWriter(
            BuilderConfigWriterOptions::fromArray($this->config->getWriter())
        );

        $this->devMode = $devMode;
        $this->configCompiler = new BuilderConfigCompiler();
    }

    /**
     * @return bool
     */
    public function isDevMode() : bool
    {
        return $this->devMode;
    }

    /**
     * @return ContainerInterface|null
     * @throws \LDL\DependencyInjection\Container\Writer\Exception\FileAlreadyExistsException
     * @throws \LDL\DependencyInjection\Service\Compiler\Exception\CompileErrorException
     * @throws \LDL\DependencyInjection\Service\Finder\Exception\NoFilesFoundException
     * @throws \LDL\Env\Finder\Exception\NoFilesFoundException
     * @throws \LDL\Env\Writer\Exception\FileAlreadyExistsException
     */
    public function getContainer(): ?ContainerInterface
    {
        if(false === $this->devMode){
            require_once __DIR__.'../../../container.php';
            $options = $this->containerConfig->getDumpOptions();

            $class = sprintf(
                '%s/%s',
                $options['namespace'],
                $options['class']
            );


            return new $this->LDLContainerBuilder;
        }

        $this->build();
        return $this->container;
    }

    /**
     * @throws \LDL\DependencyInjection\Container\Writer\Exception\FileAlreadyExistsException
     * @throws \LDL\DependencyInjection\Service\Compiler\Exception\CompileErrorException
     * @throws \LDL\DependencyInjection\Service\Finder\Exception\NoFilesFoundException
     * @throws \LDL\Env\Finder\Exception\NoFilesFoundException
     * @throws \LDL\Env\Writer\Exception\FileAlreadyExistsException
     */
    public function build()
    {
        $this->envBuilder->build();
        $this->container = $this->LDLContainerBuilder->build();
    }

    /**
     * @param string $lockFile
     *
     * @return Builder
     *
     * @throws Exception\OrchestratorConfigFileNotFoundException
     * @throws Exception\OrchestratorFSPermissionsException
     * @throws \LDL\Env\Compiler\Options\Exception\UnknownOptionException
     * @throws \LDL\Env\Finder\Options\Exception\UnknownOptionException
     */
    public static function fromConfigFile(string $lockFile) : Builder
    {
        if(false === file_exists($lockFile)){
            throw new Exception\OrchestratorConfigFileNotFoundException("File: $lockFile not found");
        }

        if(false === is_readable($lockFile)){
            throw new Exception\OrchestratorFSPermissionsException("File: $lockFile is not readable");
        }

        $config = file_get_contents($lockFile);

        return self::fromJSONString($config);
    }

    /**
     * @param string $config
     *
     * @return Builder
     *
     * @throws \LDL\Env\Compiler\Options\Exception\UnknownOptionException
     * @throws \LDL\Env\Finder\Options\Exception\UnknownOptionException
     */
    public static function fromJSONString(string $config) : Builder
    {
        $builderConfig = BuilderConfig::fromArray(json_decode($config, true, 512, \JSON_THROW_ON_ERROR));

        $orchestratorConfig = OrchestratorConfig::fromArray($builderConfig->getOrchestratorConfig());
        $envConfig = EnvConfig::fromArray($builderConfig->getEnvConfig());
        $containerConfig = ContainerConfig::fromArray($builderConfig->getContainerConfig());

        $envBuilder = new EnvBuilder(
            new EnvFileFinder(EnvFileFinderOptions::fromArray($envConfig->getFinderOptions())),
            new EnvCompiler(EnvCompilerOptions::fromArray($envConfig->getCompilerOptions()))
        );

        $containerBuilder = new LDLContainerBuilder(
            new ServiceFileFinder(ServiceFileFinderOptions::fromArray($containerConfig->getServiceFinder())),
            new ServiceCompiler(ServiceCompilerOptions::fromArray($containerConfig->getServiceCompiler())),
            new ServiceFileReader(ServiceReaderOptions::fromArray($containerConfig->getServiceReader())),
            new CompilerPassFinder(CompilerPassFinderOptions::fromArray($containerConfig->getCompilerPassFinder())),
            new CompilerPassReader(CompilerPassReaderOptions::fromArray($containerConfig->getCompilerPassReader()))
        );

        $devMode = self::ENV_PRODUCTION_NAME !== $orchestratorConfig->getMode();

        $instance = new static(
            $devMode,
            $envBuilder,
            $containerBuilder,
            $orchestratorConfig
        );

        $instance->containerConfig = $containerConfig;
        return $instance;
    }

    /**
     * Compile the main config file that is orchestrator.json
     *
     * @param OrchestratorConfig $orchestratorConfig
     * @param EnvConfig $envConfig
     * @param ContainerConfig $containerConfig
     * @return BuilderConfig
     */
    public function compileJSON(
        OrchestratorConfig $orchestratorConfig,
        EnvConfig $envConfig,
        ContainerConfig $containerConfig
    ) : BuilderConfig
    {
        return $this->configCompiler->compileJSON(
            $orchestratorConfig,
            $envConfig,
            $containerConfig
        );
    }

    /**
     * Compile all the others orchestrator.json that was found in the project
     *
     * @return BuilderConfig
     */
    public function compileLock(BuilderConfig $mainConfig) : BuilderConfig
    {
        try{

            $files = $this->configFinder->find();

        }catch(NoFileFoundException $e){

            $files = new GenericFileCollection();

        }

        return $this->configCompiler->compileLock(
            $mainConfig,
            $files,
            $this->configReader
        );
    }

    /**
     * @return EnvBuilderInterface
     */
    public function getEnvBuilder(): EnvBuilderInterface
    {
        return $this->envBuilder;
    }

    /**
     * @return LDLContainerBuilderInterface
     */
    public function getLDLContainerBuilder(): LDLContainerBuilderInterface
    {
        return $this->LDLContainerBuilder;
    }

    /**
     * @return BuilderConfigWriterInterface
     */
    public function getBuilderConfigWriter() : BuilderConfigWriterInterface
    {
        return $this->configWriter;
    }

    /**
     * @return OrchestratorConfig
     */
    public function getConfig() : OrchestratorConfig
    {
        return $this->config;
    }
}
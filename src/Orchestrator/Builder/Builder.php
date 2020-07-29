<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder;

use LDL\DependencyInjection\Container\Builder\BuilderInterface;
use LDL\Env\Builder\EnvBuilderInterface;
use LDL\Orchestrator\Builder\Config\Config\BuilderConfig;
use LDL\Orchestrator\Builder\Config\Finder\BuilderConfigFinder;
use LDL\Orchestrator\Builder\Config\Finder\BuilderConfigFinderInterface;
use LDL\Orchestrator\Builder\Config\Reader\BuilderConfigReader;
use LDL\Orchestrator\Builder\Config\Reader\BuilderConfigReaderInterface;
use LDL\Orchestrator\Builder\Config\Writer\BuilderConfigWriter;
use LDL\Orchestrator\Builder\Config\Writer\BuilderConfigWriterInterface;
use LDL\Orchestrator\Builder\Config\Writer\Exception\ConfigExistsException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Builder
{
    /**
     * @var EnvBuilderInterface
     */
    private $envBuilder;

    /**
     * @var BuilderInterface
     */
    private $containerBuilder;

    /**
     * @var BuilderConfigFinder|BuilderConfigFinderInterface
     */
    private $configFinder;

    /**
     * @var BuilderConfigReader|BuilderConfigReaderInterface
     */
    private $configReader;

    /**
     * @var BuilderConfigWriter|BuilderConfigWriterInterface
     */
    private $configWriter;

    /**
     * @var ContainerBuilder
     */
    private $container;

    public function __construct(
        EnvBuilderInterface $envBuilder,
        BuilderInterface $containerBuilder,
        BuilderConfigFinderInterface $configFinder = null,
        BuilderConfigReaderInterface $configReader = null,
        BuilderConfigWriterInterface $configWriter = null
    )
    {
        $this->envBuilder = $envBuilder;
        $this->containerBuilder = $containerBuilder;
        $this->configFinder = $configFinder ?? new BuilderConfigFinder();
        $this->configReader = $configReader ?? new BuilderConfigReader();
        $this->configWriter = $configWriter ?? new BuilderConfigWriter();
    }

    public function build(): ?BuilderConfig
    {
        $this->envBuilder->build();
        $this->container = $this->containerBuilder->build();

        if(count($this->configFinder->find()) > 0){
            return $this->configReader->read();
        }

        $config = [
            'description' => '*** DO NOT MODIFY THIS FILE MANUALLY ***',
            'filename' => $this->configWriter->getOptions()->getFilename(),
            'envFinder' => $this->envBuilder->getFinder()->getOptions()->toArray(),
            'envCompiler' => $this->envBuilder->getCompiler()->getOptions()->toArray(),
            'envWriter' => $this->envBuilder->getWriter()->getOptions()->toArray(),
            'containerFinder' => $this->containerBuilder->getFinder()->getOptions()->toArray(),
            'containerCompiler' => $this->containerBuilder->getCompiler()->getOptions()->toArray()
        ];

        $builderConfig = BuilderConfig::fromArray($config);

        try{
            $this->configWriter->write($builderConfig);

            return $builderConfig;
        }catch(ConfigExistsException $e){

        }
    }
}
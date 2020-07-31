<?php declare(strict_types=1);

namespace LDL\Orchestrator\Builder\Config\Compiler;

use LDL\DependencyInjection\Container\Config\ContainerConfig;
use LDL\Env\Config\EnvConfig;
use LDL\FS\Type\Types\Generic\Collection\GenericFileCollection;
use LDL\Orchestrator\Builder\Config\Config\BuilderConfig;
use LDL\Orchestrator\Builder\Config\Reader\BuilderConfigReaderInterface;
use LDL\Orchestrator\Config\OrchestratorConfig;

class BuilderConfigCompiler implements BuilderConfigCompilerInterface
{
    public function compileJSON(
        OrchestratorConfig $orchestratorConfig,
        EnvConfig $envConfig,
        ContainerConfig $containerConfig
    ): BuilderConfig
    {
        return BuilderConfig::fromArray([
            'description' => '*** DO NOT MODIFY THIS FILE MANUALLY ***',
            'orchestrator' => $orchestratorConfig->toArray(),
            'env' => $envConfig->toArray(),
            'container' => $containerConfig->toArray()
        ]);
    }

    public function compileLock(
        BuilderConfig $mainConfig,
        GenericFileCollection $files,
        BuilderConfigReaderInterface $configReader
    ): BuilderConfig
    {
        $configs = $mainConfig->toArray();

        foreach($files as $file){
            $config = array_merge_recursive($configs, $configReader->read($file)->toArray(['description']));
            $configs = $config;
        }

        return BuilderConfig::fromArray($configs);
    }
}
<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Loader;

use LDL\Env\Util\Loader\EnvLoader;
use LDL\Framework\Helper\ReflectionHelper;
use LDL\Orchestrator\Config\OrchestratorConfig;
use LDL\Orchestrator\Config\OrchestratorConfigInterface;
use Psr\Container\ContainerInterface;

final class OrchestratorLoader implements OrchestratorLoaderInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var OrchestratorConfigInterface
     */
    private $config;

    public static function fromJSONFile(string $file): OrchestratorLoaderInterface
    {
        $config = OrchestratorConfig::fromJSONFile($file);

        require_once (string) $config->getContainerFile();

        $class = ReflectionHelper::fromFile((string) $config->getContainerFile());
        $ns = array_keys($class)[0];
        $class = sprintf('%s\\%s', $ns, $class[$ns]['class'][0]);
        $env = $config->getEnvFile();

        /*
         * Env compilers are not needed here, the env file is already compiled in this case
         */
        EnvLoader::loadFile($env->getPath());

        $instance = new self(new $class());

        $instance->config = $config;

        $instance->container = new $class();

        return $instance;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function getConfig(): OrchestratorConfigInterface
    {
        return $this->config;
    }
}

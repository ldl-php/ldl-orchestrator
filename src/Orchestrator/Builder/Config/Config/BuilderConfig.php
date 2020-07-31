<?php declare(strict_types=1);

namespace LDL\Orchestrator\Builder\Config\Config;

use LDL\Orchestrator\Builder\Config\Interfaces\OptionsInterface;

class BuilderConfig implements OptionsInterface
{
    /**
     * @var string
     */
    private $description = '*** DO NOT MODIFY THIS FILE MANUALLY ***';

    /**
     * @var array
     */
    private $orchestratorConfig = [];

    /**
     * @var array
     */
    private $envConfig = [];

    /**
     * @var array
     */
    private $containerConfig = [];

    public static function fromArray(array $options) : self
    {
        $instance = new static();
        $defaults = $instance->toArray();
        $merge = array_replace_recursive($defaults, $options);

        return $instance->setDescription($merge['description'])
            ->setOrchestratorConfig($merge['orchestrator'])
            ->setEnvConfig($merge['env'])
            ->setContainerConfig($merge['container']);
    }

    public function toArray(array $exclude = []): array
    {
        $return = [
            'description' => $this->getDescription(),
            'orchestrator' => $this->getOrchestratorConfig(),
            'env' => $this->getEnvConfig(),
            'container' => $this->getContainerConfig()
        ];

        foreach ($return as $key => $value) {
            if (in_array($key, $exclude, true)) {
                unset($return[$key]);
                continue;
            }
        }

        return $return;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return BuilderConfig
     */
    private function setDescription(string $description): BuilderConfig
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return array
     */
    public function getOrchestratorConfig(): array
    {
        return $this->orchestratorConfig;
    }

    /**
     * @param array $orchestratorConfig
     * @return BuilderConfig
     */
    private function setOrchestratorConfig(array $orchestratorConfig): BuilderConfig
    {
        $this->orchestratorConfig = $orchestratorConfig;
        return $this;
    }

    /**
     * @return array
     */
    public function getEnvConfig(): array
    {
        return $this->envConfig;
    }

    /**
     * @param array $envConfig
     * @return BuilderConfig
     */
    private function setEnvConfig(array $envConfig): BuilderConfig
    {
        $this->envConfig = $envConfig;
        return $this;
    }

    /**
     * @return array
     */
    public function getContainerConfig(): array
    {
        return $this->containerConfig;
    }

    /**
     * @param array $containerConfig
     * @return BuilderConfig
     */
    private function setContainerConfig(array $containerConfig): BuilderConfig
    {
        $this->containerConfig = $containerConfig;
        return $this;
    }
}
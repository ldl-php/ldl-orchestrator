<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder\Config\Config;

class BuilderConfig
{
    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var array
     */
    private $envFinder;

    /**
     * @var array
     */
    private $envCompiler;

    /**
     * @var array
     */
    private $envWriter;

    /**
     * @var array
     */
    private $containerFinder;

    /**
     * @var array
     */
    private $containerCompiler;

    public static function fromArray(array $options) : self
    {
        $instance = new static();
        $defaults = $instance->toArray();
        $merge = array_merge($defaults, $options);

        return $instance->setDescription($merge['description'])
            ->setFilename($merge['filename'])
            ->setEnvFinder($merge['envFinder'])
            ->setEnvCompiler($merge['envCompiler'])
            ->setEnvWriter($merge['envWriter'])
            ->setContainerFinder($merge['containerFinder'])
            ->setContainerCompiler($merge['containerCompiler']);
    }

    public function toArray(): array
    {
        return get_object_vars($this);
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
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     * @return BuilderConfig
     */
    private function setFilename(string $filename): BuilderConfig
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return array
     */
    public function getEnvFinder(): array
    {
        return $this->envFinder;
    }

    /**
     * @param array $envFinder
     * @return BuilderConfig
     */
    private function setEnvFinder(array $envFinder): BuilderConfig
    {
        $this->envFinder = $envFinder;
        return $this;
    }

    /**
     * @return array
     */
    public function getEnvCompiler(): array
    {
        return $this->envCompiler;
    }

    /**
     * @param array $envCompiler
     * @return BuilderConfig
     */
    private function setEnvCompiler(array $envCompiler): BuilderConfig
    {
        $this->envCompiler = $envCompiler;
        return $this;
    }

    /**
     * @return array
     */
    public function getEnvWriter(): array
    {
        return $this->envWriter;
    }

    /**
     * @param array $envWriter
     * @return BuilderConfig
     */
    private function setEnvWriter(array $envWriter): BuilderConfig
    {
        $this->envWriter = $envWriter;
        return $this;
    }

    /**
     * @return array
     */
    public function getContainerFinder(): array
    {
        return $this->containerFinder;
    }

    /**
     * @param array $containerFinder
     * @return BuilderConfig
     */
    private function setContainerFinder(array $containerFinder): BuilderConfig
    {
        $this->containerFinder = $containerFinder;
        return $this;
    }

    /**
     * @return array
     */
    public function getContainerCompiler(): array
    {
        return $this->containerCompiler;
    }

    /**
     * @param array $containerCompiler
     * @return BuilderConfig
     */
    private function setContainerCompiler(array $containerCompiler): BuilderConfig
    {
        $this->containerCompiler = $containerCompiler;
        return $this;
    }

}
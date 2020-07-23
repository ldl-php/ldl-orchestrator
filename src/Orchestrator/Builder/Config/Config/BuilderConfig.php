<?php

namespace LDL\Orchestrator\Builder\Config\Config;

class BuilderConfig
{
    /**
     * @var string
     */
    private $projectDirectory;

    /**
     * @var string[]
     */
    private $directories;

    /**
     * @var string[]
     */
    private $files;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $class;

    private function __construct(){}

    public static function factory(
        string $projectDirectory,
        array $directories,
        array $files,
        string $namespace,
        string $class
    ) : BuilderConfig
    {
        $instance = new static();
        $instance->projectDirectory = $projectDirectory;
        $instance->directories = $directories;
        $instance->files = $files;
        $instance->namespace = $namespace;
        $instance->class = $class;

        return $instance;
    }

    /**
     * @return string
     */
    public function getProjectDirectory(): string
    {
        return $this->projectDirectory;
    }

    /**
     * @return string[]
     */
    public function getDirectories(): array
    {
        return $this->directories;
    }

    /**
     * @return string[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    public function toArray() : array
    {
        return get_object_vars($this);
    }

}
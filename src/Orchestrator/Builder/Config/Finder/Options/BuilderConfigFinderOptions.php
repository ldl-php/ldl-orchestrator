<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder\Config\Finder\Options;

class BuilderConfigFinderOptions
{
    /**
     * @var array
     */
    private $directories = [];

    /**
     * @var array
     */
    private $excludedDirectories = [];

    /**
     * @var string
     */
    private $file = 'orchestrator.json';

    private function __construct()
    {
    }

    public static function fromArray(array $options) : self
    {
        $instance = new static();
        $defaults = $instance->toArray();
        $merge = array_merge($defaults, $options);

        return $instance->setDirectories($merge['directories'])
            ->setFile($merge['file'])
            ->setExcludedDirectories($merge['excludedDirectories']);
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        return get_object_vars($this);
    }

    /**
     * @return array
     */
    public function jsonSerialize() : array
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function getExcludedDirectories() : array
    {
        return $this->excludedDirectories;
    }

    /**
     * @param array $directories
     * @return BuilderConfigFinderOptions
     */
    private function setExcludedDirectories(array $directories) : BuilderConfigFinderOptions
    {
        $this->excludedDirectories = $directories;
        return $this;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @param string $file
     * @return BuilderConfigFinderOptions
     */
    private function setFile(string $file): BuilderConfigFinderOptions
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @return array
     */
    public function getDirectories(): array
    {
        return $this->directories;
    }

    /**
     * @param array $directories
     * @return BuilderConfigFinderOptions
     */
    private function setDirectories(array $directories): BuilderConfigFinderOptions
    {
        if(0 === count($directories)){
            $directories[] = \getcwd();
        }

        $this->directories = $directories;
        return $this;
    }
}
<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder\Config\Reader\Options;

use LDL\Orchestrator\Builder\Config\Interfaces\OptionsInterface;

class BuilderConfigReaderOptions implements OptionsInterface
{
    /**
     * @var string
     */
    private $file = '.orchestrator-config.json';

    private function __construct()
    {
    }

    public static function fromArray(array $options) : self
    {
        $instance = new static();
        $defaults = $instance->toArray();
        $merge = array_merge($defaults, $options);

        return $instance->setFile($merge['file']);
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
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @param string $file
     * @return BuilderConfigReaderOptions
     * @throws Exception\InvalidOptionException
     */
    private function setFile(string $file): BuilderConfigReaderOptions
    {
        if('' === $file){
            throw new Exception\InvalidOptionException('No file to find were given');
        }

        $this->file = $file;
        return $this;
    }
}
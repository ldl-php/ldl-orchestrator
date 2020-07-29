<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder\Config\Writer\Options;

use LDL\Orchestrator\Builder\Config\Interfaces\OptionsInterface;

class BuilderConfigWriterOptions implements OptionsInterface
{
    /**
     * @var string
     */
    private $filename = '.orchestrator-config.json';

    /**
     * @var bool
     */
    private $force;

    public static function fromArray(array $options) : self
    {
        $instance = new static();
        $defaults = $instance->toArray();
        $merge = array_merge($defaults, $options);

        return $instance->setFilename($merge['filename'])
            ->setForce($merge['isForce']);
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
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     * @return BuilderConfigWriterOptions
     */
    public function setFilename(string $filename): BuilderConfigWriterOptions
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return bool
     */
    public function isForce(): bool
    {
        return $this->force;
    }

    /**
     * @param bool $force
     * @return BuilderConfigWriterOptions
     */
    private function setForce(bool $force): BuilderConfigWriterOptions
    {
        $this->force = $force;
        return $this;
    }
}
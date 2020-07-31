<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder\Config\Reader\Options;

use LDL\Orchestrator\Builder\Config\Interfaces\OptionsInterface;

class BuilderConfigReaderOptions implements OptionsInterface
{
    /**
     * @var bool
     */
    private $ignoreErrors = false;

    private function __construct()
    {
    }

    public static function fromArray(array $options) : self
    {
        $instance = new static();
        $defaults = $instance->toArray();
        $merge = array_merge($defaults, $options);

        return $instance->setIgnoreErrors($merge['ignoreErrors']);
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
     * @return bool
     */
    public function ignoreErrors(): bool
    {
        return $this->ignoreErrors;
    }

    /**
     * @param bool $ignoreErrors
     * @return BuilderConfigReaderOptions
     */
    private function setIgnoreErrors(bool $ignoreErrors): BuilderConfigReaderOptions
    {
        $this->ignoreErrors = $ignoreErrors;
        return $this;
    }
}
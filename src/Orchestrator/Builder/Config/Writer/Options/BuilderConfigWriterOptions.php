<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder\Config\Writer\Options;

use LDL\Orchestrator\Builder\Config\Interfaces\OptionsInterface;

class BuilderConfigWriterOptions implements OptionsInterface
{
    /**
     * @var string
     */
    private $file = 'orchestrator';

    /**
     * @var bool
     */
    private $force = false;

    /**
     * @var string
     */
    private $filePerms = '0666';

    public static function fromArray(array $options) : self
    {
        $instance = new static();
        $defaults = $instance->toArray();
        $merge = array_merge($defaults, $options);

        return $instance->setFile($merge['file'])
            ->setFilePerms($merge['filePerms'])
            ->setForce($merge['force']);
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
     * @return BuilderConfigWriterOptions
     */
    public function setFile(string $file): BuilderConfigWriterOptions
    {
        $this->file = $file;
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

    /**
     * @return string
     */
    public function getFilePerms(): string
    {
        return $this->filePerms;
    }

    /**
     * @param string $filePerms
     * @return BuilderConfigWriterOptions
     */
    private function setFilePerms(string $filePerms): BuilderConfigWriterOptions
    {
        $this->filePerms = $filePerms;
        return $this;
    }

    /**
     * @return string
     */
    public function getJsonFile() : string
    {
        return sprintf(
            '%s.json',
            $this->getFile()
        );
    }

    /**
     * @return string
     */
    public function getLockFile() : string
    {
        return sprintf(
            '%s.lock',
            $this->getFile()
        );
    }
}
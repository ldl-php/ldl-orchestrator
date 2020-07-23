<?php

namespace LDL\Orchestrator\Builder\Config\Writer;

use LDL\Orchestrator\Builder\Config\Config\BuilderConfig;

class BuilderConfigFileWriter implements BuilderConfigWriterInterface
{
    /**
     * @var string
     */
    private $outputFile;

    /**
     * @var bool
     */
    private $force;

    public function __construct(
        string $outputFile,
        bool $force=false
    )
    {
        $this->outputFile = $outputFile;
        $this->force = $force;
    }

    public function write(BuilderConfig $config) : void
    {
        if(!$this->force && file_exists($this->outputFile)){
            throw new Exception\ConfigExistsException('Configuration file already exists');
        }

        file_put_contents(
            $this->outputFile,
            json_encode($config->toArray(), JSON_PRETTY_PRINT|\JSON_THROW_ON_ERROR)
        );
    }
}
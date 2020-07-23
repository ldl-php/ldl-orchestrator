<?php

namespace LDL\Orchestrator\Builder\Config\Reader;

use LDL\Orchestrator\Builder\Config\Config\BuilderConfig;

class BuilderConfigFileReader implements BuilderConfigReaderInterface
{
    /**
     * @var string
     */
    private $file;

    public function  __construct(string $file)
    {
        $this->file = $file;
    }

    public function read(): BuilderConfig
    {
        if(!is_readable($this->file)){
            $msg = "Could not read file {$this->file}";
            throw new Exception\BuilderConfigReaderPermissionException($msg);
        }

        try {
            $config = json_decode(
                \file_get_contents($this->file),
                false,
                512,
                \JSON_THROW_ON_ERROR
            );
        }catch(\Exception $e){
            $msg = "Failed to decode file contents";
            throw new Exception\BuilderConfigReaderDecodeException($msg);
        }

        return BuilderConfig::factory(
            $config->project,
            $config->directories,
            $config->files,
            $config->namespace,
            $config->class
        );
    }

}
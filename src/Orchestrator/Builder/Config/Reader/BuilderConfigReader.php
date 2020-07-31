<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder\Config\Reader;

use LDL\FS\Type\AbstractFileType;
use LDL\Orchestrator\Builder\Config\Config\BuilderConfig;

class BuilderConfigReader implements BuilderConfigReaderInterface
{
    /**
     * @var Options\BuilderConfigReaderOptions
     */
    private $options;

    public function  __construct(Options\BuilderConfigReaderOptions $options = null)
    {
        $this->options = $options ?? Options\BuilderConfigReaderOptions::fromArray([]);
    }

    /**
     * {@inheritdoc}
     */
    public function read(AbstractFileType $file): BuilderConfig
    {
        if(!$this->options->ignoreErrors() && !$file->isReadable()){
            $msg = sprintf(
                'Could not read file "%s", file is not readable',
                $file->getRealPath()
            );

            throw new Exception\BuilderConfigReaderPermissionException($msg);
        }

        try {
            $config = json_decode(
                \file_get_contents($file->getRealPath()),
                true,
                512,
                \JSON_THROW_ON_ERROR
            );
        }catch(\Exception $e){
            $msg = "Failed to decode file contents";
            throw new Exception\BuilderConfigReaderDecodeException($msg);
        }

        return BuilderConfig::fromArray($config);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): Options\BuilderConfigReaderOptions
    {
        return $this->options;
    }

}
<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder\Config\Writer;

use LDL\Orchestrator\Builder\Config\Config\BuilderConfig;

class BuilderConfigWriter implements BuilderConfigWriterInterface
{
    /**
     * @var Options\BuilderConfigWriterOptions
     */
    private $options;

    public function __construct(Options\BuilderConfigWriterOptions $options = null)
    {
        $this->options = $options ?? Options\BuilderConfigWriterOptions::fromArray([]);
    }

    /**
     * {@inheritdoc}
     */
    public function write(BuilderConfig $config) : void
    {
        if(false === $this->options->isForce() && true === file_exists($this->options->getFilename())){
            $msg = sprintf(
                'File: %s already exists!. Force it to overwrite',
                $this->options->getFilename()
            );

            throw new Exception\ConfigExistsException($msg);
        }

        file_put_contents($this->options->getFilename(), json_encode($config->toArray(),\JSON_PRETTY_PRINT | \JSON_THROW_ON_ERROR));
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): Options\BuilderConfigWriterOptions
    {
        return clone($this->options);
    }
}
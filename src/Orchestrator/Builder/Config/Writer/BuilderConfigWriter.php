<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder\Config\Writer;

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
    public function write(string $content, bool $isJson = true) : void
    {
        $file = true === $isJson ? $this->options->getJsonFile() : $this->options->getLockFile();

        if(false === $this->options->isForce() && true === file_exists($file)){
            $msg = sprintf(
                'File: %s already exists!. Force it to overwrite',
                $file
            );

            throw new Exception\ConfigExistsException($msg);
        }

        file_put_contents($file, $content);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): Options\BuilderConfigWriterOptions
    {
        return clone($this->options);
    }
}
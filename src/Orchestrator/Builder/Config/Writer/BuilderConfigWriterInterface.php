<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder\Config\Writer;

interface BuilderConfigWriterInterface
{
    /**
     * Write builder configuration
     *
     * @param string $content
     * @param bool $isJson
     * @throws Exception\ConfigExistsException
     */
    public function write(string $content, bool $isJson = true) : void;

    /**
     * @return Options\BuilderConfigWriterOptions
     */
    public function getOptions(): Options\BuilderConfigWriterOptions;
}
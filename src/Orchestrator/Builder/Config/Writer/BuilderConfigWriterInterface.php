<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder\Config\Writer;

use LDL\Orchestrator\Builder\Config\Config\BuilderConfig;

interface BuilderConfigWriterInterface
{
    /**
     * Write builder configuration
     *
     * @param BuilderConfig $config
     * @throws Exception\ConfigExistsException
     */
    public function write(BuilderConfig $config) : void;

    /**
     * @return Options\BuilderConfigWriterOptions
     */
    public function getOptions(): Options\BuilderConfigWriterOptions;
}
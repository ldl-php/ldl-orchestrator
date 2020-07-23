<?php

namespace LDL\Orchestrator\Builder\Config\Writer;

use LDL\Orchestrator\Builder\Config\Config\BuilderConfig;

interface BuilderConfigWriterInterface
{
    /**
     * Write builder configuration
     *
     * @param BuilderConfig $config
     */
    public function write(BuilderConfig $config) : void;
}
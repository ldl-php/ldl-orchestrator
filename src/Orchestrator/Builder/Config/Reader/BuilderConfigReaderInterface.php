<?php

namespace LDL\Orchestrator\Builder\Config\Reader;

use LDL\Orchestrator\Builder\Config\Config\BuilderConfig;

interface BuilderConfigReaderInterface
{
    public function read() : BuilderConfig;
}
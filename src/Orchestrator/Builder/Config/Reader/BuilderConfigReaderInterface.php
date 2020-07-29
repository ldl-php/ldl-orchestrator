<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder\Config\Reader;

use LDL\Orchestrator\Builder\Config\Config\BuilderConfig;

interface BuilderConfigReaderInterface
{
    /**
     * @return BuilderConfig
     * @throws Exception\BuilderConfigReaderDecodeException
     * @throws Exception\BuilderConfigReaderPermissionException
     * @throws \LDL\Orchestrator\Builder\Config\Config\Exception\UnknownOptionException
     */
    public function read() : BuilderConfig;

    /**
     * @return Options\BuilderConfigReaderOptions
     */
    public function getOptions(): Options\BuilderConfigReaderOptions;
}
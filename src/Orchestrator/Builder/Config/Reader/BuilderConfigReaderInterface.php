<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder\Config\Reader;

use LDL\FS\Type\AbstractFileType;
use LDL\Orchestrator\Builder\Config\Config\BuilderConfig;

interface BuilderConfigReaderInterface
{
    /**
     * @param AbstractFileType $file
     * @return BuilderConfig
     * @throws Exception\BuilderConfigReaderDecodeException
     * @throws Exception\BuilderConfigReaderPermissionException
     */
    public function read(AbstractFileType $file) : BuilderConfig;

    /**
     * @return Options\BuilderConfigReaderOptions
     */
    public function getOptions(): Options\BuilderConfigReaderOptions;
}
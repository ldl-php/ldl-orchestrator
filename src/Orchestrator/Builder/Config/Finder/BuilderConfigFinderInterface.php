<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder\Config\Finder;

use LDL\FS\Type\Types\Generic\Collection\GenericFileCollection;

interface BuilderConfigFinderInterface
{
    /**
     * @return GenericFileCollection
     * @throws Exception\NoFileFoundException
     */
    public function find(): GenericFileCollection;

    /**
     * @return Options\BuilderConfigFinderOptions
     */
    public function getOptions(): Options\BuilderConfigFinderOptions;
}
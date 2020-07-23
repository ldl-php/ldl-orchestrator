<?php

namespace LDL\Orchestrator\CompilerPass\Finder\File;

use LDL\FS\Type\Types\Generic\Collection\GenericFileCollection;

interface CompilerPassFileFinderInterface
{
    /**
     * @param array $directories
     * @param string|null $pattern
     * @return GenericFileCollection
     */
    public function find(array $directories, string $pattern=null) : GenericFileCollection;
}
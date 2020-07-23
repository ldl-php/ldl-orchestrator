<?php

namespace LDL\Orchestrator\Services\Finder\File;

use LDL\FS\Type\Types\Generic\Collection\GenericFileCollection;

interface ServiceFileFinderInterface
{
    public function find() : GenericFileCollection;
}
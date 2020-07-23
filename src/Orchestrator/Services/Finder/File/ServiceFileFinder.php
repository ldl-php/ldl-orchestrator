<?php

namespace LDL\Orchestrator\Services\Finder\File;

use LDL\FS\Finder\Adapter\LocalFileFinder;
use LDL\FS\Type\Types\Generic\Collection\GenericFileCollection;

class ServiceFileFinder implements ServiceFileFinderInterface
{
    /**
     * @var array
     */
    private $directories;

    /**
     * @var array
     */
    private $files;

    public function __construct(
        array $directories,
        array $files
    )
    {
        $this->directories = $directories;
        $this->files = $files;
    }

    public function find() : GenericFileCollection
    {
        return LocalFileFinder::find($this->directories, $this->files);
    }
}
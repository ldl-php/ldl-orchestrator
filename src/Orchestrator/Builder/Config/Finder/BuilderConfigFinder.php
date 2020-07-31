<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder\Config\Finder;

use LDL\FS\Finder\Adapter\LocalFileFinder;
use LDL\FS\Type\Types\Generic\Collection\GenericFileCollection;
use LDL\FS\Type\Types\Generic\GenericFileType;

class BuilderConfigFinder implements BuilderConfigFinderInterface
{
    private $options;

    public function __construct(Options\BuilderConfigFinderOptions $options = null)
    {
        $this->options = $options ?? Options\BuilderConfigFinderOptions::fromArray([]);
    }

    /**
     * {@inheritdoc}
     */
    public function find(): GenericFileCollection
    {
        $return = new GenericFileCollection();

        $options =  $this->options;

        $files = LocalFileFinder::find(
            $options->getDirectories(),
            [$options->getFile()]
        );

        /**
         * @var GenericFileType $file
         */
        foreach($files as $key=>$file){
            if(in_array($file->getPath(), $options->getExcludedDirectories(), true)){
                continue;
            }

            $return->append($file);
        }

        if(!count($return)){
            $msg = sprintf(
                'No file were found matching: "%s" in directories: "%s"',
                $options->getFile(),
                implode(', ', $options->getDirectories())
            );

            throw new Exception\NoFileFoundException($msg);
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): Options\BuilderConfigFinderOptions
    {
        return $this->options;
    }
}
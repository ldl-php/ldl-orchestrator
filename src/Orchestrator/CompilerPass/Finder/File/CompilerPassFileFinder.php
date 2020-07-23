<?php

namespace LDL\Orchestrator\CompilerPass\Finder\File;

use LDL\Orchestrator\CompilerPass\Finder\Exception\CompilerPassFinderNoFilesFoundException;
use LDL\Orchestrator\CompilerPass\Validator\Exception\CompilerPassFileValidationException;
use LDL\Orchestrator\CompilerPass\Validator\File\CompilerPassFileValidatorInterface;
use LDL\FS\Finder\Adapter\LocalFileFinder;
use LDL\FS\Type\Types\Generic\Collection\GenericFileCollection;

class CompilerPassFileFinder
{
    private const DEFAULT_FILE_PATTERN = '^.*CompilerPass.php$';

    /**
     * @var CompilerPassFileValidatorInterface
     */
    private $fileValidator;

    /**
     * @var array
     */
    private $errors = [];

    public function __construct(
        CompilerPassFileValidatorInterface $fileValidator
    )
    {
        $this->fileValidator = $fileValidator;
    }

    /**
     * @param array $directories
     * @param string|null $pattern
     * @return GenericFileCollection
     * @throws CompilerPassFinderNoFilesFoundException
     */
    public function find(array $directories, string $pattern=null) : GenericFileCollection
    {
        $compilerPassFileRegex = $pattern ?? self::DEFAULT_FILE_PATTERN;
        $files = LocalFileFinder::findRegex($compilerPassFileRegex, $directories);

        if(!count($files)){
            $msg = "No compiler passes were found";
            throw new CompilerPassFinderNoFilesFoundException($msg);
        }

        foreach($files as $file){
            try {
                $this->fileValidator->validate($file);
            }catch(CompilerPassFileValidationException $e){
                $this->errors[] = [
                    'file' => $file,
                    'exception' => $e,
                ];
            }
        }

        return $files;
    }

    public function getErrors() : array
    {
        return $this->errors;
    }

}
<?php

namespace LDL\Orchestrator\CompilerPass\Validator\File;

class CompilerPassFileValidator implements CompilerPassFileValidatorInterface
{
    public function validate(\SplFileInfo $compilerPassFile) : void
    {
        preg_match_all(
            '/class.*implements.*CompilerPassInterface.*/i',
            file_get_contents($compilerPassFile->getRealPath()),
            $classesInFile
        );

        if(empty($classesInFile)){
            $msg = sprintf(
                'Could not find any compiler pass class defined in file: "%s"',
                $compilerPassFile->getRealPath()
            );

            throw new Exception\ClassNotFoundException($msg);
        }

        $amountOfClasses = count($classesInFile);

        if($amountOfClasses > 1){
            $msg = sprintf(
                'You may define only ONE compiler pass per file, %s defined in file: "%s"',
                $amountOfClasses,
                $compilerPassFile->getRealPath()
            );

            throw new Exception\MultipleClassesDefinedException($msg);
        }
    }
}
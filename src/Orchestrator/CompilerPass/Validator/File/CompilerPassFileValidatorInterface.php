<?php

namespace LDL\Orchestrator\CompilerPass\Validator\File;

interface CompilerPassFileValidatorInterface
{
    /**
     * Checks that a class that implements the CompilerPassInterface exists in the file
     * Checks that there are no multiple classes defined in said file.
     *
     * @param \SplFileInfo $compilerPassFile
     *
     * @throws Exception\ClassNotFoundException When no classes are found
     * @throws Exception\MultipleClassesDefinedException When there are multiple classes defined in the same file
     *
     * @return void
     */
    public function validate(\SplFileInfo $compilerPassFile) : void;
}
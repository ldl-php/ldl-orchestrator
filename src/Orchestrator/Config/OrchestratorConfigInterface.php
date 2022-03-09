<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Config;

use LDL\File\Collection\ReadableFileCollection;
use LDL\File\Contracts\FileInterface;
use LDL\Framework\Base\Contracts\ArrayFactoryInterface;
use LDL\Framework\Base\Contracts\JsonFactoryInterface;
use LDL\Framework\Base\Contracts\JsonFileFactoryInterface;
use LDL\Framework\Base\Contracts\Type\ToArrayInterface;

interface OrchestratorConfigInterface extends \JsonSerializable, ToArrayInterface, ArrayFactoryInterface, JsonFileFactoryInterface, JsonFactoryInterface
{
    public function getContainerFile(): FileInterface;

    public function getServiceFiles(): ReadableFileCollection;

    public function getEnvFile(): FileInterface;

    public function getEnvFiles(): ReadableFileCollection;

    public function getCompilerPassFiles(): ReadableFileCollection;

    public function write(string $path, bool $force): FileInterface;
}

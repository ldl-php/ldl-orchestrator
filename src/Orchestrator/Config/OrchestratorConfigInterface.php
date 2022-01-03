<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Config;

use LDL\File\Collection\ReadableFileCollection;
use LDL\File\Contracts\FileInterface;
use LDL\Framework\Base\Contracts\ArrayFactoryInterface;
use LDL\Framework\Base\Contracts\Type\ToArrayInterface;

interface OrchestratorConfigInterface extends \JsonSerializable, ToArrayInterface, ArrayFactoryInterface
{
    public static function fromJSONString(string $json): OrchestratorConfigInterface;

    public static function fromJSONFile(string $file): OrchestratorConfigInterface;

    public function getContainerFile(): FileInterface;

    public function getServiceFiles(): ReadableFileCollection;

    public function getEnvFile(): FileInterface;

    public function getEnvFiles(): ReadableFileCollection;

    public function getCompilerPassFiles(): ReadableFileCollection;
}

<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Config;

use LDL\File\Collection\ReadableFileCollection;
use LDL\File\Constants\FileTypeConstants;
use LDL\File\Contracts\FileInterface;
use LDL\File\Exception\FileException;
use LDL\File\Exception\FileReadException;
use LDL\File\File;
use LDL\Framework\Base\Exception\LDLException;
use LDL\Framework\Helper\IterableHelper;
use LDL\Orchestrator\Config\Exception\OrchestratorConfigException;

final class OrchestratorConfig implements OrchestratorConfigInterface
{
    /**
     * @var FileInterface
     */
    private $containerFile;

    /**
     * @var ReadableFileCollection
     */
    private $serviceFiles;

    /**
     * @var ReadableFileCollection
     */
    private $compilerPassFiles;

    /**
     * @var FileInterface
     */
    private $envFile;

    /**
     * @var ReadableFileCollection
     */
    private $envFiles;

    public function __construct(
        FileInterface $containerFile,
        ReadableFileCollection $serviceFiles,
        ReadableFileCollection $compilerPassFiles,
        FileInterface $envFile,
        ReadableFileCollection $envFiles
    ) {
        $this->containerFile = $containerFile;
        $this->serviceFiles = $serviceFiles;
        $this->compilerPassFiles = $compilerPassFiles;
        $this->envFile = $envFile;
        $this->envFiles = $envFiles;
    }

    //<editor-fold desc="ArrayFactoryInterface methods">
    public static function fromArray(array $data = []): OrchestratorConfigInterface
    {
    }
    //</editor-fold>

    public static function fromJSONString(string $json): OrchestratorConfigInterface
    {
        $json = json_decode($json, true);

        if (!array_key_exists('container', $json)) {
            throw new Exception\OrchestratorConfigSectionMissingException('No container section found');
        }

        $container = $json['container'];

        if (!array_key_exists('file', $container)) {
            throw new Exception\OrchestratorConfigSectionMissingException('No file section found in container section');
        }

        try {
            $containerFile = new File($json['container']['file']);
        } catch (FileException $e) {
            throw new Exception\OrchestratorConfigException($e->getMessage(), $e->getCode(), $e);
        }

        if (!array_key_exists('files', $container['service'])) {
            throw new Exception\OrchestratorConfigSectionMissingException('No service files section found in container section');
        }

        if (!array_key_exists('files', $container['passes'])) {
            throw new Exception\OrchestratorConfigSectionMissingException('No compiler passes section found in container section');
        }

        if (!array_key_exists('env', $json)) {
            throw new Exception\OrchestratorConfigSectionMissingException('No env section found');
        }

        $env = $json['env'];

        if (!array_key_exists('file', $env)) {
            throw new Exception\OrchestratorConfigSectionMissingException('No file section found in env section');
        }

        if (!array_key_exists('files', $env)) {
            throw new Exception\OrchestratorConfigSectionMissingException('No files section found in env section');
        }

        try {
            return new self(
                $containerFile,
                new ReadableFileCollection($container['service']['files']),
                new ReadableFileCollection($container['passes']['files']),
                new File($env['file']),
                new ReadableFileCollection($container['env']['files'])
            );
        } catch (LDLException $e) {
            throw new Exception\OrchestratorConfigException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public static function fromJSONFile(string $file): OrchestratorConfigInterface
    {
        $file = new File($file);

        if (FileTypeConstants::FILE_TYPE_REGULAR !== $file->getType()) {
            throw new OrchestratorConfigException(sprintf('Invalid file type: "%s", "%s" is not a valid orchestrator config file', $file->getPath(), $file->getType()));
        }

        if (!$file->isReadable()) {
            throw new FileReadException(sprintf('Config file: %s is not readable', $file->getPath()));
        }

        return self::fromJSONString(file_get_contents($file->getPath()));
    }

    public function getContainerFile(): FileInterface
    {
        return $this->containerFile;
    }

    public function getServiceFiles(): ReadableFileCollection
    {
        return $this->serviceFiles;
    }

    public function getEnvFile(): FileInterface
    {
        return $this->envFile;
    }

    public function getEnvFiles(): ReadableFileCollection
    {
        return $this->envFiles;
    }

    public function getCompilerPassFiles(): ReadableFileCollection
    {
        return $this->compilerPassFiles;
    }

    //<editor-fold desc="ToArrayInterface methods">
    public function toArray(bool $useKeys = null): array
    {
        return [
            'description' => '*** DO NOT MODIFY THIS FILE MANUALLY ***',
            'container' => [
                'file' => $this->containerFile->getPath(),
                'service' => [
                    'files' => IterableHelper::map($this->serviceFiles, static function ($f) {
                        return $f->getPath();
                    }),
                ],
                'passes' => [
                    'files' => IterableHelper::map($this->compilerPassFiles, static function ($f) {
                        return $f->getPath();
                    }),
                ],
            ],
            'env' => [
                'file' => $this->envFile->getPath(),
                'files' => IterableHelper::map($this->envFiles, static function ($f) {
                    return $f->getPath();
                }),
            ],
        ];
    }
    //</editor-fold>

    //<editor-fold desc="\JsonSerializable methods">
    public function jsonSerialize()
    {
        return $this->toArray(true);
    }
    //</editor-fold>
}

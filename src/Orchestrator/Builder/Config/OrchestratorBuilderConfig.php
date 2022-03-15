<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder\Config;

use LDL\DependencyInjection\CompilerPass\Finder\Options\CompilerPassFileFinderOptions;
use LDL\DependencyInjection\CompilerPass\Finder\Options\CompilerPassFileFinderOptionsInterface;
use LDL\DependencyInjection\Container\Options\ContainerDumpOptions;
use LDL\DependencyInjection\Container\Options\ContainerDumpOptionsInterface;
use LDL\DependencyInjection\Service\File\Finder\Options\ServiceFileFinderOptions;
use LDL\DependencyInjection\Service\File\Finder\Options\ServiceFileFinderOptionsInterface;
use LDL\Env\File\Finder\Options\EnvFileFinderOptions;
use LDL\Env\File\Finder\Options\EnvFileFinderOptionsInterface;
use LDL\File\Contracts\FileInterface;
use LDL\File\File;
use LDL\Orchestrator\Builder\Config\Exception\OrchestratorBuilderConfigException;
use LDL\Orchestrator\Builder\Config\Exception\OrchestratorBuilderConfigWriteException;

class OrchestratorBuilderConfig implements OrchestratorBuilderConfigInterface
{
    /**
     * @var EnvFileFinderOptionsInterface
     */
    private $envFileFinderOptions;

    /**
     * @var ServiceFileFinderOptionsInterface
     */
    private $serviceFileFinderOptions;

    /**
     * @var CompilerPassFileFinderOptionsInterface
     */
    private $compilerPassFileFinderOptions;

    /**
     * @var ContainerDumpOptionsInterface
     */
    private $dumpOptions;

    public function __construct(
        EnvFileFinderOptionsInterface $envFileFinderOptions,
        ServiceFileFinderOptionsInterface $serviceFileFinderOptions,
        CompilerPassFileFinderOptionsInterface $compilerPassFileFinderOptions,
        ContainerDumpOptionsInterface $dumpOptions
    ) {
        $this->envFileFinderOptions = $envFileFinderOptions;
        $this->serviceFileFinderOptions = $serviceFileFinderOptions;
        $this->compilerPassFileFinderOptions = $compilerPassFileFinderOptions;
        $this->dumpOptions = $dumpOptions;
    }

    public static function fromArray(array $data = []): OrchestratorBuilderConfigInterface
    {
        /*
         * @TODO Check indices
         */
        return new self(
            EnvFileFinderOptions::fromArray($data['env']),
            ServiceFileFinderOptions::fromArray($data['service']),
            CompilerPassFileFinderOptions::fromArray($data['cpass']),
            ContainerDumpOptions::fromArray($data['options'])
        );
    }

    public function toArray(bool $useKeys = null): array
    {
        return [
            'env' => $this->envFileFinderOptions->toArray(),
            'service' => $this->serviceFileFinderOptions->toArray(),
            'cpass' => $this->compilerPassFileFinderOptions->toArray(),
            'options' => $this->dumpOptions,
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public static function fromJsonFile(string $file): OrchestratorBuilderConfigInterface
    {
        try {
            return self::fromJsonString((new File($file))->getLinesAsString());
        } catch (\Throwable $e) {
            $msg = sprintf(
                'Could not create %s from json file %s',
                __CLASS__,
                $file
            );
            throw new OrchestratorBuilderConfigException($msg, 0, $e);
        }
    }

    public static function fromJsonString(string $json): OrchestratorBuilderConfigInterface
    {
        try {
            return self::fromArray(json_decode($json, true, 2048, \JSON_THROW_ON_ERROR));
        } catch (\Throwable $e) {
            $msg = sprintf('Could not create %s from json string', __CLASS__);
            throw new OrchestratorBuilderConfigException($msg, 0, $e);
        }
    }

    public function getEnvFileFinderOptions(): EnvFileFinderOptionsInterface
    {
        return $this->envFileFinderOptions;
    }

    public function getServiceFileFinderOptions(): ServiceFileFinderOptionsInterface
    {
        return $this->serviceFileFinderOptions;
    }

    public function getCompilerPassFileFinderOptions(): CompilerPassFileFinderOptionsInterface
    {
        return $this->compilerPassFileFinderOptions;
    }

    public function getDumpOptions(): ContainerDumpOptionsInterface
    {
        return $this->dumpOptions;
    }

    public function write(string $path, bool $force = false): FileInterface
    {
        try {
            return File::create(
                $path,
                json_encode($this, \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT),
                0644,
                $force
            );
        } catch (\Throwable $e) {
            $msg = sprintf(
                'Could not write orchestrator builder config to file %s',
                $path
            );

            throw new OrchestratorBuilderConfigWriteException($msg, 0, $e);
        }
    }
}

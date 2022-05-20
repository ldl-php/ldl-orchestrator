<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Dumper;

use LDL\DependencyInjection\Container\Dumper\LDLContainerDumper;
use LDL\DependencyInjection\Container\Options\ContainerDumpOptionsInterface;
use LDL\File\Contracts\DirectoryInterface;
use LDL\Framework\Base\Exception\InvalidArgumentException;
use LDL\Orchestrator\Compiler\CompiledOrchestratorInterface;
use LDL\Orchestrator\Orchestrator;

class OrchestratorDumper implements OrchestratorDumperInterface
{
    /**
     * @var ContainerDumpOptionsInterface
     */
    private $dumpOptions;

    /**
     * @var string
     */
    private $dumpFormat;

    /**
     * @var string
     */
    private $containerFilename;

    /**
     * @var string
     */
    private $envFilename;

    public function __construct(
        ContainerDumpOptionsInterface $dumpOptions,
        string $dumpFormat = null,
        string $containerFilename = null,
        string $envFilename = null
    ) {
        $this->containerFilename = $containerFilename ?? Orchestrator::DEFAULT_CONTAINER_FILE_NAME;
        $this->envFilename = $envFilename ?? Orchestrator::DEFAULT_ENV_FILE_NAME;
        $this->dumpFormat = $dumpFormat ?? LDLContainerDumper::DUMP_FORMAT_PHP;
        $this->dumpOptions = $dumpOptions;
    }

    public function dump(CompiledOrchestratorInterface $compiled, DirectoryInterface $output): void
    {
        if (LDLContainerDumper::DUMP_FORMAT_PHP_EVAL === $this->dumpFormat) {
            throw new InvalidArgumentException(sprintf('Dump format "%s" generates no files, can not dump', $this->dumpFormat));
        }

        $containerContents = LDLContainerDumper::dump(
            $this->dumpFormat,
            $compiled->getContainer(),
            $this->dumpOptions
        );

        /*
         * Write container file
         */
        $output->mkfile(
            $this->containerFilename,
            $containerContents,
            0644,
            true
        );

        /*
         * Write env file
         */
        $output->mkfile(
            $this->envFilename,
            (string) $compiled->getEnvLines(),
            0644,
            true
        );
    }
}

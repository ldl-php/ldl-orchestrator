<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder;

use LDL\DependencyInjection\CompilerPass\Finder\CompilerPassFileFinderInterface;
use LDL\DependencyInjection\Container\Builder\LDLContainerBuilderInterface;
use LDL\DependencyInjection\Container\Dumper\LDLContainerDumper;
use LDL\DependencyInjection\Container\Options\ContainerDumpOptions;
use LDL\DependencyInjection\Container\Options\ContainerDumpOptionsInterface;
use LDL\DependencyInjection\Service\File\Finder\ServiceFileFinderInterface;
use LDL\Env\Builder\EnvBuilderInterface;
use LDL\Env\File\Finder\EnvFileFinderInterface;
use LDL\File\Collection\ReadableFileCollection;
use LDL\File\Contracts\DirectoryInterface;
use LDL\File\File;
use LDL\Orchestrator\Builder\Config\OrchestratorBuilderConfig;
use LDL\Orchestrator\Builder\Config\OrchestratorBuilderConfigInterface;
use LDL\Orchestrator\Config\OrchestratorConfig;
use LDL\Orchestrator\Config\OrchestratorConfigInterface;

class OrchestratorBuilder implements OrchestratorBuilderInterface
{
    /**
     * @var EnvFileFinderInterface
     */
    private $envFileFinder;

    /**
     * @var ServiceFileFinderInterface
     */
    private $serviceFileFinder;

    /**
     * @var EnvBuilderInterface
     */
    private $envBuilder;

    /**
     * @var LDLContainerBuilderInterface
     */
    private $containerBuilder;

    /**
     * @var CompilerPassFileFinderInterface
     */
    private $compilerPassFileFinder;

    /**
     * @var ContainerDumpOptionsInterface
     */
    private $dumpOptions;

    public function __construct(
        EnvFileFinderInterface $envFileFinder,
        ServiceFileFinderInterface $serviceFileFinder,
        CompilerPassFileFinderInterface $compilerPassFileFinder,
        EnvBuilderInterface $envBuilder,
        LDLContainerBuilderInterface $containerBuilder,
        ContainerDumpOptionsInterface $dumpOptions = null
    ) {
        $this->envFileFinder = $envFileFinder;
        $this->serviceFileFinder = $serviceFileFinder;
        $this->compilerPassFileFinder = $compilerPassFileFinder;
        $this->envBuilder = $envBuilder;
        $this->containerBuilder = $containerBuilder;
        $this->dumpOptions = $dumpOptions ?? new ContainerDumpOptions();
    }

    public function getConfig(): OrchestratorBuilderConfigInterface
    {
        return new OrchestratorBuilderConfig(
          $this->envFileFinder->getOptions(),
          $this->serviceFileFinder->getOptions(),
          $this->compilerPassFileFinder->getOptions(),
          $this->dumpOptions
        );
    }

    public function build(DirectoryInterface $output): OrchestratorConfigInterface
    {
        $envFiles = $this->envFileFinder->find();
        $serviceFiles = $this->serviceFileFinder->find();
        $compilerPassFiles = $this->compilerPassFileFinder->find();

        $containerContents = LDLContainerDumper::dump(
            LDLContainerDumper::DUMP_FORMAT_PHP,
            $this->containerBuilder->build($serviceFiles, $compilerPassFiles),
            $this->dumpOptions
        );

        $containerFile = $output->mkfile(
            sprintf('ldl-container-%s.php', sha1($containerContents)),
            $containerContents,
            0644,
            true
        )->link($output->mkpath('ldl-container.php'), true);

        $envContents = (string) $this->envBuilder->build($envFiles);

        $envFile = $output->mkfile(
            sprintf('ldl-env-%s', sha1($envContents)),
            $envContents,
            0644,
            true
        )->link($output->mkpath('ldl-env'), true);

        return new OrchestratorConfig(
            $containerFile->getTarget(),
            new ReadableFileCollection($serviceFiles->map(static function ($f) {
                return new File($f->getPath());
            })),
            new ReadableFileCollection($compilerPassFiles->map(static function ($f) {
                return new File($f->getPath());
            })),
            $envFile->getTarget(),
            new ReadableFileCollection($envFiles->map(static function ($f) {
                return new File($f->getPath());
            }))
        );
    }

    public function getEnvFileFinder(): EnvFileFinderInterface
    {
        return $this->envFileFinder;
    }

    public function getServiceFileFinder(): ServiceFileFinderInterface
    {
        return $this->serviceFileFinder;
    }

    public function getEnvBuilder(): EnvBuilderInterface
    {
        return $this->envBuilder;
    }

    public function getContainerBuilder(): LDLContainerBuilderInterface
    {
        return $this->containerBuilder;
    }

    public function getCompilerPassFileFinder(): CompilerPassFileFinderInterface
    {
        return $this->compilerPassFileFinder;
    }

    public function getDumpOptions(): ContainerDumpOptionsInterface
    {
        return $this->dumpOptions;
    }
}

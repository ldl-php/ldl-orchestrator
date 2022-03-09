<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder;

use LDL\DependencyInjection\CompilerPass\Finder\CompilerPassFileFinderInterface;
use LDL\DependencyInjection\Container\Builder\LDLContainerBuilderInterface;
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
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;

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
     * @var array
     */
    private $dumpOptions;

    public function __construct(
        EnvFileFinderInterface $envFileFinder,
        ServiceFileFinderInterface $serviceFileFinder,
        CompilerPassFileFinderInterface $compilerPassFileFinder,
        EnvBuilderInterface $envBuilder,
        LDLContainerBuilderInterface $containerBuilder,
        array $dumpOptions = []
    ) {
        $this->envFileFinder = $envFileFinder;
        $this->serviceFileFinder = $serviceFileFinder;
        $this->compilerPassFileFinder = $compilerPassFileFinder;
        $this->envBuilder = $envBuilder;
        $this->containerBuilder = $containerBuilder;
        $this->dumpOptions = $dumpOptions;
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

        $containerContents = (
            new PhpDumper(
                $this->containerBuilder
                    ->build($serviceFiles, $compilerPassFiles)
            )
        )->dump($this->dumpOptions);

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
}

<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Loader;

use LDL\DependencyInjection\Container\Dumper\LDLContainerDumper;
use LDL\DependencyInjection\Container\Options\ContainerDumpOptions;
use LDL\DependencyInjection\Container\Options\ContainerDumpOptionsInterface;
use LDL\Env\Util\Loader\EnvLoader;
use LDL\File\Contracts\DirectoryInterface;
use LDL\Orchestrator\Builder\BuiltOrchestratorInterface;
use LDL\Orchestrator\Orchestrator;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OrchestratorLoader implements OrchestratorLoaderInterface
{
    /**
     * @var ContainerDumpOptionsInterface
     */
    private $dumpOptions;

    /**
     * @var string
     */
    private $envFilename;

    /**
     * @var string
     */
    private $containerFilename;

    public function __construct(
        ContainerDumpOptionsInterface $dumpOptions = null,
        string $envFilename = null,
        string $containerFilename = null
    ) {
        $this->envFilename = $envFilename ?? Orchestrator::DEFAULT_ENV_FILE_NAME;
        $this->containerFilename = $containerFilename ?? Orchestrator::DEFAULT_CONTAINER_FILE_NAME;
        $this->dumpOptions = $dumpOptions ?? new ContainerDumpOptions();
    }

    public function load(BuiltOrchestratorInterface $compiledOrchestrator): ContainerInterface
    {
        if (!$compiledOrchestrator->getContainer()->isCompiled()) {
            $compiledOrchestrator->getContainer()->compile();
        }

        if (count($compiledOrchestrator->getEnvLines()) > 0) {
            EnvLoader::load($compiledOrchestrator->getEnvLines());
        }

        $code = LDLContainerDumper::dump(
            LDLContainerDumper::DUMP_FORMAT_PHP_EVAL,
            $compiledOrchestrator->getContainer(),
            $this->dumpOptions
        );

        /*
         * I don't even like it, but arguably requiring a container file is the same, plus we don't want to create
         * a file in this particular case.
         */
        eval($code);

        $class = sprintf('%s\\%s', $this->dumpOptions->getNamespace(), $this->dumpOptions->getClass());

        return new $class();
    }

    public function loadDirectory(DirectoryInterface $directory): ContainerInterface
    {
        $containerFilename = $this->containerFilename;
        $envFilename = $this->envFilename;

        $class = sprintf('%s\\%s', $this->dumpOptions->getNamespace(), $this->dumpOptions->getClass());

        $envPath = $directory->mkpath($envFilename);

        if (file_exists($envPath)) {
            EnvLoader::loadFile($envPath);
        }

        $containerFile = $directory->mkpath($containerFilename);

        if (!file_exists($containerFile)) {
            throw new Exception\OrchestratorLoaderException("Could not find file $containerFile");
        }

        require_once $containerFile;

        if (!class_exists($class)) {
            $msg = "Class $class does not exists after loading container file";
            throw new Exception\OrchestratorLoaderException($msg);
        }

        return new $class();
    }
}

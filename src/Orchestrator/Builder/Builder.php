<?php

namespace LDL\Orchestrator\Builder;

use LDL\Orchestrator\Builder\Config\Config\BuilderConfig;
use LDL\Orchestrator\CompilerPass\Validator\File\CompilerPassFileValidator;
use LDL\FS\Finder\Adapter\LocalFileFinder;
use LDL\FS\Type\Types\Generic\Collection\GenericFileCollection;
use LDL\FsUtil\util\Fs;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Finder\SplFileInfo;

class Builder
{
    private const ENV_FILE = '.env';

    private const ORCHESTRATOR_CONFIG_FILE = '.orchestrator-config.json';

    private const DEFAULT_CONTAINER_NAMESPACE = 'LDL\\framework';

    private const DEFAULT_CONTAINER_CLASS = 'DIC';

    /**
     * @var BuilderConfig
     */
    private $config;

    /**
     * @var array
     */
    private $envFileData;

    /**
     * @var array
     */
    private $directories;

    /**
     * @var GenericFileCollection
     */
    private $compilerPasses;

    /**
     * @var \IteratorAggregate
     */
    private $devCompilerPasses;

    /**
     * @var GenericFileCollection
     */
    private $foundFiles;

    /**
     * @var array
     */
    private $envFiles;

    /**
     * @var array
     */
    private $errors;

    /**
     * @var string
     */
    private $outputFile;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $class;

    /**
     * @var array
     */
    private $files;

    /**
     * @var string
     */
    private $projectDirectory;

    /**
     * Orchestrator constructor. Can only be accessed through static factory method
     * @see self::factory
     */
    private function __construct()
    {
    }

    public static function factory(
        string $projectDirectory,
        array $directories,
        array $files,
        string $namespace = null,
        string $class=null
    ): self
    {
        $instance = new static();

        $instance->namespace = $namespace ?? self::DEFAULT_CONTAINER_NAMESPACE;
        $instance->class = $class ?? self::DEFAULT_CONTAINER_CLASS;
        $instance->projectDirectory = $projectDirectory;
        $instance->directories = $directories;
        $instance->files = $files;

        return $instance;
    }

    /**
     * Returns if the application is in development mode or not.
     */
    public function appIsInDevMode(bool $cache = null): bool
    {
        return array_key_exists('DEV_MODE', $this->envFileData) &&
            (bool) $this->envFileData['DEV_MODE'];
    }

    public function findServiceFiles(bool $cache = true) : GenericFileCollection
    {
        if($cache && null !== $this->foundFiles){
            return $this->foundFiles;
        }

        return $this->foundFiles = LocalFileFinder::find($this->getDirectories(), $this->files);
    }

    public function findCompilerPasses(
        string $compilerPassFileRegex = '^.*CompilerPass.php$',
        bool $cache=true
    ) : GenericFileCollection
    {
        if($cache && null !== $this->compilerPasses){
            return $this->compilerPasses;
        }

        return $this->compilerPasses = LocalFileFinder::findRegex($compilerPassFileRegex, $this->getDirectories());
    }

    private function getDirectories() : array
    {
        $projectDirectory = $this->projectDirectory;
        /**
         * Append to each directory, the project directory
         */
        $directories = array_map(function($directory) use ($projectDirectory){
            $dir = Fs::mkPath($projectDirectory, $directory);
            $dir = realpath($dir);
            return $dir;
        }, $this->directories);

        return array_filter($directories, function($item){
            return false !== $item;
        });
    }

    /**
     * Compiles project container.
     *
     * @param bool $devMode
     * @param string $configFile
     * @param ProgressBar $progress
     *
     * @throws \Exception
     *
     * @return ContainerBuilder|null
     */
    public function compile(
        bool $devMode = false,
        string $configFile = null,
        ProgressBar $progress = null
    ): ?ContainerBuilder
    {
        $builder = new ContainerBuilder();

        $builder->setParameter('ldl.project.directory', $this->projectDirectory);

        $serviceFiles = \iterator_to_array($this->findServiceFiles());

        if($configFile) {
            $loadConfigFirst = Fs::mkPath($this->projectDirectory, $configFile);
            array_unshift($serviceFiles, new SplFileInfo($loadConfigFirst, '', ''));
        }

        $devCompilerPasses = [];
        $compilerPasses = $this->findCompilerPasses();

        if(null !== $progress){
            $progress->setMaxSteps(
                count($devCompilerPasses) +
                count($serviceFiles) +
                count(\iterator_to_array($compilerPasses))
            );
        }

        /**
         * @var SplFileInfo $file
         */
        foreach ($serviceFiles as $file) {
            $locator = new FileLocator($file->getPath());
            $loader = new XmlFileLoader($builder, $locator);

            try {
                $loader->load($file);
                if($progress){
                    $progress->advance();
                }
            } catch (\Exception $e) {
                $msg = "Failed to load service file: {$e->getMessage()}";
                throw new Exception\OrchestratorServiceFileLoadException($msg);
            }
        }

        foreach ($compilerPasses as $pass) {
            $instance = $this->getCompilerPassInstanceFromFile($pass);

            $builder->addCompilerPass($instance);

            if($progress){
                $progress->advance();
            }
        }

        $builder->compile();

        return $builder;
    }

    private function writeOrchestratorConfig() : void
    {
        $config = [
            'description' => '*** DO NOT MODIFY THIS FILE MANUALLY ***',
            'namespace' => $this->namespace,
            'class' => $this->class,
            'containerFile' => $this->outputFile,
            'project' => $this->projectDirectory,
            'directories' => $this->directories,
            'files' => $this->files
        ];

        file_put_contents(
            Fs::mkPath($this->projectDirectory, self::ORCHESTRATOR_CONFIG_FILE),
            json_encode($config,\JSON_PRETTY_PRINT | \JSON_THROW_ON_ERROR)
        );
    }

    public function write(
        ContainerBuilder $builder,
        string $outputFile,
        int $permissions = 0666,
        bool $forceRewrite = false
    ): string {

        $this->outputFile = $outputFile;
        $namespace = $this->namespace;
        $class = $this->class;

        $outputFile = new \SplFileInfo($outputFile);
        $containerFileExists = file_exists($outputFile);

        if(!$forceRewrite && $containerFileExists){
            $msg = "Container file: \"$outputFile\" already exists";
            throw new Exception\OrchestratorContainerExistsException($msg);
        }

        if (
            !is_dir($outputFile->getPath()) &&
            false === @mkdir($outputFile->getPath(), 0755) &&
            !is_dir($outputFile->getPath())
        ) {
            $msg = "Tried to create the container directory \"{$outputFile->getPath()}\" but I couldn't, check FS permissions";
            throw new Exception\OrchestratorException($msg);
        }

        /**
         * Lock the container file exclusively so there is no race condition when recreating it
         */

        $fp = fopen($outputFile, 'wb');
        flock($fp, \LOCK_EX);

        $dumper = new PhpDumper($builder);

        $result = file_put_contents(
            $outputFile,
            $dumper->dump([
                'namespace' => $namespace,
                'class' => $class,
            ])
        );

        if (false === $result) {
            $msg = "Could not write container to file: \"$outputFile\" check file permissions";
            throw new Exception\OrchestratorException($msg);
        }

        flock($fp, \LOCK_UN);
        fclose($fp);

        chmod($outputFile, $permissions);

        $this->writeOrchestratorConfig();

        return $outputFile;
    }

    public function getContainerInstance() : Container
    {
        if(!file_exists($this->outputFile)){
            $msg = "Container file \"{$this->outputFile}\" was not found, build the container first.";
            throw new Exception\OrchestratorContainerFileNotFound($msg);
        }

        $containerClass = sprintf(
            '%s\\%s',
            $this->namespace,
            $this->class
        );

        $containerClass = preg_replace('#\\\\#', '\\', $containerClass);

        if(!class_exists($containerClass)) {
            require $this->outputFile;
        }

        return new $containerClass;
    }

    public function generateEnvCache(string $envCacheFile)
    {
        /**
         * Find all env files.
         */
        $envFiles = LocalFileFinder::findMatching('.env', $this->getDirectories());

        if (file_exists($envCacheFile)) {
            if (!is_readable($envCacheFile)) {
                $msg = "Env cache file: \"$envCacheFile\" is not readable";
                throw new \RuntimeException($msg);
            }

            unlink($envCacheFile);
        }

        foreach ($envFiles as $env) {
            file_put_contents($envCacheFile, "{$env->getRealPath()}\n", \FILE_APPEND);
        }
    }

    public function loadEnvFromCacheFile(string $file, bool $single = false): bool
    {
        $file = trim($file);

        if (!is_readable($file)) {
            return false;
        }

        if (true === $single) {
            $loader = new Dotenv();

            $loader->load($file);

            return true;
        }

        /*
         * Load all env files from cached env file
         */
        foreach (file($file) as $env) {
            $this->loadEnvFromCacheFile($env, true);
        }

        return true;
    }

    /**
     * Obtains a compiler pass instance from a file
     *
     * @param SplFileInfo $compilerPassFile
     * @return CompilerPassInterface
     *
     * @throws Exception\OrchestratorCompilerPassException
     */
    private function getCompilerPassInstanceFromFile(SplFileInfo $compilerPassFile) : CompilerPassInterface
    {
        CompilerPassFileValidator::validate($compilerPassFile);

        require $compilerPassFile->getRealPath();

        $class = get_declared_classes();
        $class = $class[count($class) - 1];

        $passInstance = new $class();

        $rc = new \ReflectionObject($passInstance);

        if(!$rc->implementsInterface(CompilerPassInterface::class)){
            $msg = sprintf(
                'Compiler pass does not implement the correct compiler pass interface (%s), at file: %s',
                CompilerPassInterface::class,
                $compilerPassFile->getRealPath()
            );
            throw new Exception\OrchestratorCompilerPassException($msg);
        }

        return $passInstance;
    }
}

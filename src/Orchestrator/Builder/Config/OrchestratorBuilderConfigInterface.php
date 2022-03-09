<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder\Config;

use LDL\DependencyInjection\CompilerPass\Finder\Options\CompilerPassFileFinderOptionsInterface;
use LDL\DependencyInjection\Service\File\Finder\Options\ServiceFileFinderOptionsInterface;
use LDL\Env\File\Finder\Options\EnvFileFinderOptionsInterface;
use LDL\File\Contracts\FileInterface;
use LDL\Framework\Base\Contracts\ArrayFactoryInterface;
use LDL\Framework\Base\Contracts\JsonFactoryInterface;
use LDL\Framework\Base\Contracts\JsonFileFactoryInterface;
use LDL\Framework\Base\Contracts\Type\ToArrayInterface;
use LDL\Orchestrator\Builder\Config\Exception\OrchestratorBuilderConfigWriteException;

interface OrchestratorBuilderConfigInterface extends \JsonSerializable, JsonFileFactoryInterface, JsonFactoryInterface, ArrayFactoryInterface, ToArrayInterface
{
    /**
     * Obtains env file finder options.
     */
    public function getEnvFileFinderOptions(): EnvFileFinderOptionsInterface;

    /**
     * Obtains service file finder options.
     */
    public function getServiceFileFinderOptions(): ServiceFileFinderOptionsInterface;

    /**
     * Obtains compiler pass file finder options.
     */
    public function getCompilerPassFileFinderOptions(): CompilerPassFileFinderOptionsInterface;

    /**
     * Returns an array of container dump options.
     */
    public function getDumpOptions(): array;

    /**
     * @throws OrchestratorBuilderConfigWriteException
     */
    public function write(string $path, bool $force = false): FileInterface;
}

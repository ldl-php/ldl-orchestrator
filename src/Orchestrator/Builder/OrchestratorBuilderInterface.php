<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder;

use LDL\File\Contracts\DirectoryInterface;
use LDL\Orchestrator\Builder\Config\OrchestratorBuilderConfigInterface;
use LDL\Orchestrator\Config\OrchestratorConfigInterface;

interface OrchestratorBuilderInterface
{
    /**
     * Builds the container and writes settings from which the container was built
     * returns a productive config which can be used to load orchestrator through OrchestratorLoader.
     */
    public function build(DirectoryInterface $directory): OrchestratorConfigInterface;

    /**
     * Obtains builder config, this config can be used later to rebuild the container
     * with the same options.
     */
    public function getConfig(): OrchestratorBuilderConfigInterface;
}

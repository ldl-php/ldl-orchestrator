<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder;

use LDL\File\Directory;

interface OrchestratorBuilderInterface
{
    public function build(Directory $directory, array $dumpOptions);
}

#!/usr/bin/env php
<?php declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use LDL\Orchestrator\Console\Command\OrchestratorBuildCommand;
use Symfony\Component\Console\Application;

$console = new Application('orchestrator');

$console->addCommands([
    new OrchestratorBuildCommand(),
]);

$console->run();

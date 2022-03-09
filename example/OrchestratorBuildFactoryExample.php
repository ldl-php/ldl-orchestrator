<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/autoload.php';

use LDL\File\Directory;
use LDL\File\Exception\FileException;
use LDL\File\Helper\DirectoryHelper;
use LDL\File\Helper\FilePathHelper;
use LDL\Orchestrator\Builder\Factory\OrchestratorBuilderFactory;
use LDL\Orchestrator\Console\Command\OrchestratorBuildCommand;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;

$path = FilePathHelper::createAbsolutePath(sys_get_temp_dir(), 'ldl-orchestrator-example');
try {
    $outputDirectory = Directory::create($path, 0755);
} catch (FileException $e) {
    DirectoryHelper::delete($path);
    $outputDirectory = Directory::create($path, 0755);
}

$argv = new ArgvInput([],
    new InputDefinition([
        new InputArgument(
            'output-directory',
            InputArgument::OPTIONAL,
            '',
            $outputDirectory->getPath()
        ),
        new InputArgument(
            'directories',
            InputArgument::OPTIONAL,
            'Directories to search',
            FilePathHelper::createAbsolutePath(__DIR__, 'Build')
        ),
        new InputOption(
            'dump-options',
            'c',
            InputOption::VALUE_REQUIRED,
            'Path to JSON file containing container options',
            FilePathHelper::createAbsolutePath(__DIR__, 'container-options.json')
        ),
        new InputOption(
            'force',
            'f',
            InputOption::VALUE_OPTIONAL,
            'Force creation',
            true
        ),
    ])
);

$build = new OrchestratorBuildCommand();

$build->execute($argv, new ConsoleOutput());

$orchBuilder = OrchestratorBuilderFactory::fromJsonFile($outputDirectory->mkpath('ldl-orchestrator-build-config.json'));
dump($orchBuilder->getConfig()->toArray());

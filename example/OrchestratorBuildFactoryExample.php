<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/autoload.php';

use LDL\DependencyInjection\CompilerPass\Finder\Options\CompilerPassFileFinderOptions;
use LDL\DependencyInjection\Container\Options\ContainerDumpOptions;
use LDL\DependencyInjection\Service\File\Finder\Options\ServiceFileFinderOptions;
use LDL\Env\File\Finder\Options\EnvFileFinderOptions;
use LDL\File\Directory;
use LDL\File\Helper\DirectoryHelper;
use LDL\File\Helper\FilePathHelper;
use LDL\Orchestrator\Builder\Config\OrchestratorBuilderConfig;
use LDL\Orchestrator\Builder\Factory\OrchestratorBuilderFactory;
use LDL\Orchestrator\Loader\OrchestratorLoader;

$srcDir = new Directory(
    FilePathHelper::createAbsolutePath(__DIR__, 'Build')
);

$outDir = DirectoryHelper::getSysTempDir()->mkdir('ldl-orchestrator-example', 0755, true);

$orchBuildConfig = (new OrchestratorBuilderConfig(
    EnvFileFinderOptions::fromArray([
        'directories' => [$srcDir->getPath()],
    ]),
    ServiceFileFinderOptions::fromArray([
        'directories' => [
            $srcDir->getPath(),
        ],
        'files' => [
            'applications.xml',
            'routes.xml',
            'modules.xml',
            'services.xml',
            'commands.xml',
            'parameters.xml',
        ],
    ]),
    CompilerPassFileFinderOptions::fromArray([
        'directories' => [$srcDir->getPath()],
    ]),
    ContainerDumpOptions::fromArray([
        'namespace' => '\My\Custom\NS',
        'class' => sprintf('%sContainer', 'Example'),
    ])
))->write($outDir->mkpath('ldl-orchestrator-build.config'));

$build = OrchestratorBuilderFactory::fromJsonFile($outDir->mkpath('ldl-orchestrator-build.config'))
    ->build($outDir);

$build->write($outDir->mkpath('ldl-orchestrator.json'));

$loader = OrchestratorLoader::fromJsonFile($outDir->mkpath('ldl-orchestrator.json'));

echo "\n################################################\n";
echo "Writing to $outDir ...\n";
echo "################################################\n\n";

echo "Check some environment variables ...\n\n";

var_dump('ADMIN_APPLICATION_URL', getenv('ADMIN_APPLICATION_URL'));
var_dump('USER_APPLICATION_URL', getenv('USER_APPLICATION_URL'));

echo "\nPrint out service ID's plus referenced classes ...\n\n";

foreach ($loader->getContainer()->getServiceIDs() as $serviceID) {
    echo sprintf('%s: %s%s', $serviceID, get_class($loader->getContainer()->get($serviceID)), "\n");
}

if (isset($_SERVER['argv'][1])) {
    $outDir->delete();
}

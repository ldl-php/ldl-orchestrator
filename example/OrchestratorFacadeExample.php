<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/Build/autoload.php';

use LDL\File\Collection\DirectoryCollection;
use LDL\File\Directory;
use LDL\File\Helper\FilePathHelper;
use LDL\Orchestrator\Builder\OrchestratorBuilder;
use LDL\Orchestrator\Collection\OrchestratorCollection;
use LDL\Orchestrator\Facade\OrchestratorFacade;
use LDL\Orchestrator\Loader\OrchestratorLoader;
use LDL\Type\Collection\Types\String\StringCollection;

$loader = new OrchestratorLoader();

$orchestrators = new OrchestratorCollection([
    OrchestratorFacade::fromDirectory(new DirectoryCollection([
        new Directory(FilePathHelper::createAbsolutePath(__DIR__, 'Build')),
    ]), new StringCollection(['services.xml'])),
]);

$container = $loader->load((new OrchestratorBuilder($orchestrators))->build());
var_dump(getenv('ADMIN_APPLICATION_URL'));
echo "Check some environment variables ...\n\n";

var_dump('ADMIN_APPLICATION_URL', getenv('ADMIN_APPLICATION_URL'));
var_dump('USER_APPLICATION_URL', getenv('USER_APPLICATION_URL'));

echo "\nPrint out service ID's plus referenced classes ...\n\n";

foreach ($container->getServiceIDs() as $serviceID) {
    echo sprintf('%s: %s%s', $serviceID, get_class($container->get($serviceID)), "\n");
}

echo "\nPrint out a parameter mapped to .env ...\n\n";

dump($container->getParameter('admin.url'));

echo "\n";

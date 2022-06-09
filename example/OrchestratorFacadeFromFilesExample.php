<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/Build/autoload.php';

use LDL\File\Helper\FilePathHelper;
use LDL\Orchestrator\Facade\OrchestratorFacade;
use LDL\Orchestrator\Loader\OrchestratorLoader;

$fromFiles = OrchestratorFacade::fromFiles(
    [
        FilePathHelper::createAbsolutePath(__DIR__, 'Build', 'Application', 'Admin', 'services.xml'),
        FilePathHelper::createAbsolutePath(__DIR__, 'Build', 'Application', 'User', 'services.xml'),
        FilePathHelper::createAbsolutePath(__DIR__, 'Build', 'Framework', 'Services', 'Mailer', 'services.xml'),
        FilePathHelper::createAbsolutePath(__DIR__, 'Build', 'Framework', 'Services', 'Template', 'services.xml'),
    ],
    [
        FilePathHelper::createAbsolutePath(__DIR__, 'Build', 'Application', 'Admin', '.env'),
        FilePathHelper::createAbsolutePath(__DIR__, 'Build', 'Application', 'User', '.env'),
    ]
);

$loader = new OrchestratorLoader();
$container = $loader->load($fromFiles);
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

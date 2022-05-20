<?php

declare(strict_types=1);

use LDL\File\Helper\FilePathHelper;

spl_autoload_register(static function ($className) {
    $className = substr($className, (int) strpos($className, '\\', 5));

    $file = FilePathHelper::createAbsolutePath(
        __DIR__,
        '..',
        sprintf('%s.php', str_replace('\\', DIRECTORY_SEPARATOR, $className))
    );

    require $file;
});

<?php

namespace LDL\Orchestrator\Console;

use LDL\FS\Finder\Adapter\LocalFileFinder;
use LDL\FsUtil\util\Fs;
use Symfony\Component\Console\Application as SymfonyApplication;

class Console extends SymfonyApplication
{
    const BANNER = <<<'BANNER'
BANNER;

    public function __construct(string $name = 'UNKNOWN', string $version = 'UNKNOWN')
    {
        echo self::BANNER;
        parent::__construct('<info>[ Orchestrator project builder ]</info>', $version);

        $commands = LocalFileFinder::findRegex(
            '^.*\.php$',
            [
                Fs::mkPath(__DIR__, 'Command')
            ]
        );

        $commands = array_map(function($item) {
            return $item->getRealPath();
        },\iterator_to_array($commands));

        usort($commands, function($a, $b){
           return strcmp($a, $b);
        });

        /**
         * @var \SplFileInfo $commandFile
         */
        foreach($commands as $key => $commandFile){
            /**
             * Skip abstract class, there is no need to require it due to autoloader kicking in
             */
            if(0 === $key){
                continue;
            }
            require $commandFile;

            $class = get_declared_classes();
            $class = $class[count($class) - 1];

            $this->add(new $class());
        }
    }
}

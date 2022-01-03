<?php

declare(strict_types=1);

namespace LDL\Example\Build\Framework\Application;

use LDL\DependencyInjection\CompilerPass\LDLAbstractCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ApplicationCompilerPass extends LDLAbstractCompilerPass
{
    private const TAG = 'example.application';
    private const COLLECTION = 'example.application.collection';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::COLLECTION)) {
            return;
        }

        $collection = $container->findDefinition(self::COLLECTION);

        $tags = $container->findTaggedServiceIds(self::TAG);

        foreach ($tags as $id => $tag) {
            $collection->addMethodCall('add', [new Reference($id)]);
        }
    }
}

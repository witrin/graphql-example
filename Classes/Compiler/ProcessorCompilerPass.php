<?php

namespace Vendor\Example\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Vendor\Example\Data\ResultHandler;

class ProcessorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(ResultHandler::class)) {
            return;
        }

        $definition = $container->findDefinition(ResultHandler::class);

        $taggedServices = $container->findTaggedServiceIds('graphql.processor');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('addProcessor', [new Reference($id), $attributes['identifier']]);
            }
        }
    }
}

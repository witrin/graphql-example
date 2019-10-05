<?php

namespace Vendor\Example;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Vendor\Example\Compiler\ProcessorCompilerPass;

return function (ContainerConfigurator $container, ContainerBuilder $containerBuilder) {
    $containerBuilder->addCompilerPass(new ProcessorCompilerPass());
};

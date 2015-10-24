<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AddServicesCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('ms.rpc.route_loader')) {
            return;
        }

        $definition = $container->findDefinition('ms.rpc.route_loader');

        $services = $container->findTaggedServiceIds('ms.rpc.service');
        foreach ($services as $id => $tags) {
            $service = new Reference($id);

            $definition->addMethodCall('addService', [$id, $service, $tags]);
        }
    }
}

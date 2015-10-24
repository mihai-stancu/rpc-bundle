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
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class AddConnectionsCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $services = $container->findTaggedServiceIds('ms.rpc.connection');
        foreach ($services as $id => $tags) {
            $definition = $container->findDefinition($id);

            $this->addConnection($definition, $id, $tags);
        }
    }

    /**
     * @param Definition $definition
     * @param string     $id
     * @param array      $tags
     */
    protected function addConnection(Definition $definition, $id, $tags)
    {
        $definition->addMethodCall('setRequestFactory', [new Reference('ms.rpc.request_factory')]);
        $definition->addMethodCall('setResponseFactory', [new Reference('ms.rpc.response_factory')]);
        $definition->addMethodCall('setSerializer', [new Reference('serializer')]);
        $definition->setLazy(true);
    }
}

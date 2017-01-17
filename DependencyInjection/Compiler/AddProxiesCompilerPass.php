<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\DependencyInjection\Compiler;

use MS\RpcBundle\Proxy\Generator;
use MS\RpcBundle\Proxy\Proxy;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class AddProxiesCompilerPass implements CompilerPassInterface
{
    /** @var  Generator */
    protected $proxyGenerator;

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $directory = $container->getParameter('ms_rpc.proxy.directory');
        $namespace = $container->getParameter('ms_rpc.proxy.namespace');
        $baseTrait = $container->getParameter('ms_rpc.proxy.base_trait');
        $this->proxyGenerator = new Generator($directory, $namespace, $baseTrait);

        $services = $container->findTaggedServiceIds('ms.rpc.proxy');
        foreach ($services as $id => $tags) {
            $definition = $container->findDefinition($id);

            $this->addProxy($definition, $id, $tags);
        }
    }

    /**
     * @param Definition $definition
     * @param string     $id
     * @param array      $tags
     *
     * @throws InvalidConfigurationException
     */
    protected function addProxy(Definition $definition, $id, $tags)
    {
        $interfaceOrClass = $definition->getClass();
        if (!interface_exists($interfaceOrClass) and !class_exists($interfaceOrClass)) {
            $message = sprintf('RPC Proxy service definition supplied class (%s) does not exist', $interfaceOrClass);
            throw new InvalidConfigurationException($message);
        }

        $tag = reset($tags);
        if (!isset($tag['connection'])) {
            $message = sprintf('RPC Proxy service definition supplied tags does not specify a connection', $interfaceOrClass);
            throw new InvalidConfigurationException($message);
        }
        $connection = $tag['connection'];

        $remote_service = $id;
        if (isset($tag['remote_service'])) {
            $remote_service = $tag['remote_service'];
        }

        if (!is_subclass_of($interfaceOrClass, Proxy::class)) {
            $proxyClass = $this->proxyGenerator->generateProxyClass($interfaceOrClass);

            $definition->setClass($proxyClass);
        }

        $definition->setArguments([new Reference($connection), $remote_service]);
    }
}

<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle;

use MS\RpcBundle\DependencyInjection\Compiler\AddConnectionsCompilerPass;
use MS\RpcBundle\DependencyInjection\Compiler\AddProxiesCompilerPass;
use MS\RpcBundle\DependencyInjection\Compiler\AddServicesCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MSRpcBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddConnectionsCompilerPass());
        $container->addCompilerPass(new AddProxiesCompilerPass());
        $container->addCompilerPass(new AddServicesCompilerPass());
    }

    public function boot()
    {
        $directory = $this->container->getParameter('ms_rpc.proxy.directory');
        $namespace = $this->container->getParameter('ms_rpc.proxy.namespace');

        $autoloader = function ($class) use ($directory, $namespace) {
            if (0 === strpos($class, $namespace)) {
                $file = $directory.str_replace([$namespace, '\\'], ['', '/'], $class).'.php';

                require $file;
            }
        };

        spl_autoload_register($autoloader);
    }
}

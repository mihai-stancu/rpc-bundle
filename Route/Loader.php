<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Route;

use Symfony\Component\Config\Loader\Loader as SymfonyLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class Loader extends SymfonyLoader
{
    /**
     * @var bool
     */
    protected $loaded = false;

    /**
     * @var array|object[]
     */
    protected $services = [];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param string $resource
     * @param string $type
     *
     * @return bool
     */
    public function supports($resource, $type = null)
    {
        return 'ms.rpc.service' === $type;
    }

    /**
     * @param string $resource
     * @param string $type
     *
     * @return RouteCollection
     */
    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "rpc_loader" loader twice');
        }

        $routes = new RouteCollection();

        foreach ($this->services as $id => $service) {
            $options = !empty($this->options[$id][0]) ? $this->options[$id][0] : [];
            $this->loadRoutes($routes, $id, $service, $options);
        }

        $this->loaded = true;

        return $routes;
    }

    /**
     * @param RouteCollection $routes
     * @param string          $id
     * @param object          $service
     * @param array           $options
     */
    protected function loadRoutes($routes, $id, $service, array $options = [])
    {
        $class = get_class($service);
        if (!empty($options['interface'])) {
            $class = $options['interface'];
        }

        $regex = '/^[^_]{1,2}\w+/';
        if (!empty($options['regex'])) {
            $regex = $options['regex'];
        }

        $methods = get_class_methods($class);
        $methods = preg_grep($regex, $methods);
        foreach ($methods as $method) {
            $name = $id.':'.$method;
            $controller = 'MSRpcBundle:Rpc:dispatch';
            $route = new Route('/', ['_controller' => $controller], []);
            $routes->add($name, $route);
        }
    }

    /**
     * @param string $id
     * @param object $service
     * @param array  $options
     */
    public function addService($id, $service, array $options = [])
    {
        $this->services[$id] = $service;
        $this->options[$id] = $options;
    }
}

<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\EventListener;

use MS\RpcBundle\Factory\RequestFactory;
use MS\RpcBundle\Model\RpcXRequest;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RequestListener
{
    /** @var RequestFactory */
    protected $factory;

    /** @var  Router */
    protected $router;

    /**
     * @param RequestFactory $factory
     * @param Router         $router
     */
    public function __construct(RequestFactory $factory, Router $router)
    {
        $this->factory = $factory;
        $this->router = $router;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (!$this->factory->validate($request)) {
            return;
        }

        $rpcRequest = $this->factory->createFrom($request);
        $rpcRoute = $rpcRequest->getMethod();
        if ($rpcRequest instanceof RpcXRequest) {
            $rpcRoute = $rpcRequest->getService().':'.$rpcRoute;
        }

        if (!($route = $this->router->getRouteCollection()->get($rpcRoute))) {
            return;
        }

        $controller = $route->getDefault('_controller');
        $request->attributes->set('_controller', $controller);
        $request->attributes->set('_route', $rpcRoute);
        $request->attributes->set('rpcRequest', $rpcRequest);
    }
}

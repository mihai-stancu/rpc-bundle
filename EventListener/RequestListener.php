<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\EventListener;

use MS\RpcBundle\Factory\RequestFactory;
use MS\RpcBundle\Model\Rpc\Request as RpcRequest;
use MS\RpcBundle\Model\RpcX\Request as RpcXRequest;
use MS\RpcBundle\RpcException;
use Symfony\Component\Routing\Router;
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
     *
     * @throws RpcException
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $requestType = $request->headers->get('Content-Type');
        $responseType = $request->headers->get('Accept');

        if (!$this->factory->validate($requestType) or !$this->factory->validate($responseType)) {
            return;
        }

        $rpcRequest = $this->factory->createFrom($request);
        $rpcRoute = $this->getRouteName($rpcRequest);

        $route = $this->router->getRouteCollection()->get($rpcRoute);
        if (!$route) {
            $message = sprintf('Route %s not found', $rpcRoute);
            throw new RpcException($message);
        }

        $controller = $route->getDefault('_controller');
        $request->attributes->set('_controller', $controller);
        $request->attributes->set('_route', $rpcRoute);
        $request->attributes->set('_rpcRequest', $rpcRequest);
    }

    /**
     * @param RpcRequest $rpcRequest
     *
     * @return string
     */
    public function getRouteName(RpcRequest $rpcRequest)
    {
        $method = $rpcRequest->getMethod();
        if ($rpcRequest instanceof RpcXRequest) {
            $method = $rpcRequest->getService().':'.$method;
        }

        return $method;
    }
}

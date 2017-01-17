<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Controller;

use MS\RpcBundle\Factory\RequestFactory;
use MS\RpcBundle\Factory\ResponseFactory;
use MS\RpcBundle\Model\Rpc\Request as RpcRequest;
use MS\RpcBundle\Model\Rpc\Response as RpcResponse;
use MS\RpcBundle\Model\Rpc\Response as RpcXResponse;
use MS\RpcBundle\Model\RpcX\Request as RpcXRequest;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class RpcController implements ContainerAwareInterface
{
    /** @var  ContainerInterface */
    protected $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param Request    $request
     * @param RpcRequest $rpcRequest
     *
     * @return RpcResponse|RpcXResponse
     */
    public function dispatchAction(Request $request, RpcRequest $rpcRequest)
    {
        /** @var RequestFactory $requestFactory */
        $requestFactory = $this->container->get('ms.rpc.request_factory');

        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->container->get('ms.rpc.response_factory');

        if ($rpcRequest instanceof RpcXRequest) {
            $service = $rpcRequest->getService();
            $method = $rpcRequest->getMethod();
        } else {
            $method = $rpcRequest->getMethod();
            list($service, $method) = explode(':', $method);
        }

        $service = $this->container->get($service);
        $method = new \ReflectionMethod($service, $method);

        $params = $rpcRequest->getParams();
        $params = $requestFactory->mapParams($method, $params);

        try {
            $result = $method->invokeArgs($service, $params);
        } catch (\Exception $result) {
        }

        $rpcResponse = $responseFactory->createFrom($request, $rpcRequest, $result);

        return $rpcResponse;
    }
}

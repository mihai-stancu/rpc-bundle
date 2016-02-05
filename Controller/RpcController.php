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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RpcController extends Controller
{
    /**
     * @param Request    $request
     * @param RpcRequest $rpcRequest
     *
     * @return RpcResponse|RpcXResponse
     */
    public function dispatchAction(Request $request, RpcRequest $rpcRequest)
    {
        /** @var RequestFactory $requestFactory */
        $requestFactory = $this->get('ms.rpc.request_factory');

        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->get('ms.rpc.response_factory');

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

            /* @TODO: remove json_encode/json_decode circular reference hack */
            $result = json_encode($result);
            $result = json_decode($result, true);
        } catch (\Exception $result) {
        }

        $rpcResponse = $responseFactory->createFrom($request, $rpcRequest, $result);

        return $rpcResponse;
    }
}

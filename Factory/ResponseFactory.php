<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Factory;

use MS\RpcBundle\Model\Rpc\Error;
use MS\RpcBundle\Model\Rpc\Request as RpcRequest;
use MS\RpcBundle\Model\Rpc\Response as RpcResponse;
use MS\RpcBundle\Model\RpcX\Response as RpcXResponse;
use MS\RpcBundle\RpcException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ResponseFactory extends AbstractFactory
{
    /**
     * @param $protocol
     *
     * @return mixed
     */
    public function className($protocol)
    {
        return static::$protocols['response'][$protocol];
    }

    /**
     * Create RPC request object for the specified protocol.
     *
     * @param string $protocol
     *
     * @return RpcResponse|RpcXResponse
     */
    public function create($protocol)
    {
        $class = static::$protocols['response'][$protocol];

        return new $class();
    }

    /**
     * Create RPC response object from HTTP response object.
     *
     * @param Request    $request
     * @param RpcRequest $rpcRequest
     * @param mixed      $result
     *
     * @throws RpcException
     *
     * @return RpcResponse|RpcXResponse
     */
    public function createFrom(Request $request, RpcRequest $rpcRequest = null, $result)
    {
        $requestType = $request->headers->get('Content-Type');
        $responseType = $request->headers->get('Accept');

        if (!$this->validate($requestType) or !$this->validate($responseType)) {
            throw new RpcException('Invalid protocol or encoding');
        }

        list($protocol, $encoding) = $this->getContentType($requestType);

        $rpcResponse = $this->create($protocol);

        if ($rpcRequest !== null) {
            $rpcResponse->setVersion($rpcRequest->getVersion());
            $rpcResponse->setId($rpcRequest->getId());
        }

        if ($result instanceof \Exception) {
            $error = new Error();
            $error->setCode($result->getCode());
            $error->setMessage($result->getMessage());
            $error->setData($result->getTraceAsString());

            $rpcResponse->setError($error);

            return $rpcResponse;
        }

        $rpcResponse->setResult($result);

        return $rpcResponse;
    }
}

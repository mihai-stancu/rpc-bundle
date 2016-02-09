<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Factory;

use MS\RpcBundle\Model\Rpc\Request as RpcRequest;
use MS\RpcBundle\Model\RpcX\Request as RpcXRequest;
use MS\RpcBundle\RpcException;
use Symfony\Component\HttpFoundation\Request;

class RequestFactory extends AbstractFactory
{
    /**
     * @param $protocol
     *
     * @return mixed
     */
    public function className($protocol)
    {
        return static::$protocols['request'][$protocol];
    }

    /**
     * Create RPC request object for the specified protocol.
     *
     * @param string $protocol
     *
     * @return RpcRequest|RpcXRequest
     */
    public function create($protocol)
    {
        $class = static::$protocols['request'][$protocol];

        return new $class();
    }

    /**
     * Create RPC request object from HTTP request object.
     *
     * @param Request           $request
     * @param \ReflectionMethod $reflectionMethod
     *
     * @throws RpcException
     *
     * @return RpcRequest|RpcXRequest
     */
    public function createFrom(Request $request, \ReflectionMethod $reflectionMethod = null)
    {
        $requestType = $request->headers->get('Content-Type');
        $responseType = $request->headers->get('Accept');

        if (!$this->validate($requestType) or !$this->validate($responseType)) {
            throw new RpcException('Invalid protocol or encoding');
        }

        list($protocol, $encoding) = $this->getContentType($requestType);
        $class = $this->className($protocol);
        $context = [
            'encoding' => $encoding,
            'reflectionParameters' => $reflectionMethod->getParameters(),
        ];

        if (method_exists($class, 'factory')) {
            return $class::factory($request, $this->serializer, $context);
        }

        $content = $request->getContent();
        $rpcRequest = $this->serializer->deserialize($content, $class, $protocol, $context);

        return $rpcRequest;
    }
}

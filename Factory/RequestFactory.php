<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Factory;

use MS\RpcBundle\Model\RpcRequest;
use MS\RpcBundle\Model\RpcXRequest;
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
        if (!$this->validate($request)) {
            throw new RpcException('Invalid protocol or encoding');
        }

        $protocol = $request->headers->get('RPC-Request-Type');
        $encoding = $request->getContentType();

        $class = $this->className($protocol);
        $context = ['encoding' => $encoding];
        $content = $request->getContent();

        /** @var RpcRequest|RpcXRequest $rpcRequest */
        $rpcRequest = $this->serializer->deserialize($content, $class, $protocol, $context);

        if (!empty($reflectionMethod)) {
            $rpcParams = $rpcRequest->getParams();
            $rpcParams = $this->mapParams($reflectionMethod, $rpcParams);
            $rpcRequest->setParams($rpcParams);
        }

        return $rpcRequest;
    }

    /**
     * Map received values to a methods parameters.
     *
     * @param \ReflectionMethod $method
     * @param array             $values
     *
     * @return array
     */
    public function mapParams($method, array $values)
    {
        $arguments = [];
        foreach ($method->getParameters() as $param) {
            $index = $param->getPosition();

            $arguments[$index] = $this->mapParam($param, $values);
        }
        ksort($arguments);

        $output = [];
        foreach ($method->getParameters() as $param) {
            $name = $param->getName();
            $index = $param->getPosition();

            $output[$name] = $arguments[$index];
        }

        return $output;
    }

    /**
     * @param \ReflectionParameter $param
     * @param array                $values
     *
     * @throws RpcException
     *
     * @return mixed
     */
    protected function mapParam(\ReflectionParameter $param, array $values)
    {
        $name = $param->getName();
        $index = $param->getPosition();

        if (array_key_exists($name, $values)) {
            $value = $values[$name];
        } elseif (array_key_exists($index, $values)) {
            $value = $values[$index];
        } elseif ($param->isDefaultValueAvailable()) {
            $value = $param->getDefaultValue();
        } else {
            $message = sprintf('Missing parameter #%s: %s', $index, $name);
            throw new RpcException($message);
        }

        if ($param->getClass()) {
            $class = $param->getClass()->getName();
            $value = $this->serializer->denormalize($value, $class);
        }

        return $value;
    }
}

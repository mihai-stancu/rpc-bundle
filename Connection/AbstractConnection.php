<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Connection;

use MS\RpcBundle\Factory\RequestFactory;
use MS\RpcBundle\Factory\ResponseFactory;
use MS\RpcBundle\Model\Rpc\Interfaces\Request as RpcRequestInterface;
use MS\RpcBundle\Model\Rpc\Interfaces\Response as RpcResponseInterface;
use MS\RpcBundle\Model\RpcX\Interfaces\Request as RpcXRequestInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractConnection implements Connection, SerializerAwareInterface, ContainerAwareInterface
{
    /** @var  ContainerInterface */
    protected $container;

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /** @var  SerializerInterface|DenormalizerInterface */
    protected $serializer;

    /**
     * @return SerializerInterface
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * @param SerializerInterface $serializer
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /** @var  RequestFactory */
    protected $requestFactory;

    /**
     * @return RequestFactory
     */
    public function getRequestFactory()
    {
        return $this->requestFactory;
    }

    /**
     * @param RequestFactory $requestFactory
     */
    public function setRequestFactory($requestFactory)
    {
        $this->requestFactory = $requestFactory;
    }

    /** @var  ResponseFactory */
    protected $responseFactory;

    /**
     * @return ResponseFactory
     */
    public function getResponseFactory()
    {
        return $this->responseFactory;
    }

    /**
     * @param ResponseFactory $responseFactory
     */
    public function setResponseFactory($responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /** @var  string */
    protected $protocol;

    /** @var  string */
    protected $encoding;

    /** @var  bool */
    protected $synchronous;

    /** @var array */
    protected $endpoint;

    /**
     * @param array $context
     * @param array $endpoint
     */
    public function __construct(array $context = [], array $endpoint = [])
    {
        list($this->protocol, $this->encoding, $this->synchronous) = $context;

        $this->endpoint = $this->prepareEndpoint($endpoint);
    }

    /**
     * @param array $endpoint
     *
     * @throws InvalidConfigurationException
     *
     * @returns array
     */
    protected function prepareEndpoint(array $endpoint = [])
    {
        $message = sprintf('%s does not implement the %s method', get_called_class(), __FUNCTION__);
        throw new InvalidConfigurationException($message);
    }

    /**
     * @param RpcRequestInterface $rpcRequest
     *
     * @throws InvalidConfigurationException
     *
     * @return RpcResponseInterface|null
     */
    protected function sendRequest(RpcRequestInterface $rpcRequest)
    {
        $message = sprintf('%s does not implement the %s method', get_called_class(), __FUNCTION__);
        throw new InvalidConfigurationException($message);
    }

    /**
     * @param string $service
     * @param string $method
     * @param array  $params
     *
     * @throws InvalidConfigurationException
     *
     * @return mixed
     */
    public function send($service, $method, array $params = [])
    {
        $rpcProtocol = $this->protocol;
        $rpcRequest = $this->getRequestFactory()->create($rpcProtocol);

        if ($this->synchronous) {
            $rpcId = bin2hex(openssl_random_pseudo_bytes(16));
            $rpcRequest->setId($rpcId);
        }

        $rpcRequest->setMethod($service.':'.$method);
        if ($rpcRequest instanceof RpcXRequestInterface) {
            $rpcRequest->setService($service);
            $rpcRequest->setMethod($method);
        }

        $rpcRequest->setParams($params);
        $rpcResponse = $this->sendRequest($rpcRequest);

        $result = null;
        if ($rpcResponse instanceof RpcResponseInterface) {
            $result = $rpcResponse->getResult();
        }

        return $result;
    }
}

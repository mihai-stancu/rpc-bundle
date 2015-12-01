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
use MS\RpcBundle\Model\RpcRequest;
use MS\RpcBundle\Model\RpcResponse;
use MS\RpcBundle\Model\RpcXRequest;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractConnection implements SerializerAwareInterface, ConnectionInterface
{
    /** @var  SerializerInterface|DenormalizerInterface */
    protected $serializer;

    /** @var  RequestFactory */
    protected $requestFactory;

    /** @var  ResponseFactory */
    protected $responseFactory;

    /** @var  string */
    protected $protocol;

    /** @var  string */
    protected $encoding;

    /** @var array */
    protected $endpoint;

    /** @var  bool */
    protected $synchronous;

    /**
     * @param string $protocol
     * @param string $encoding
     * @param bool   $synchronous
     * @param array  $endpoint
     */
    public function __construct(
        $protocol,
        $encoding,
        $synchronous = false,
        array $endpoint = []
    ) {
        $this->protocol = $protocol;
        $this->encoding = $encoding;
        $this->synchronous = $synchronous;
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
     * @param RpcRequest $rpcRequest
     * @param string     $resultType
     *
     * @throws InvalidConfigurationException
     *
     * @return RpcResponse|null
     */
    protected function sendRequest(RpcRequest $rpcRequest, $resultType = null)
    {
        $message = sprintf('%s does not implement the %s method', get_called_class(), __FUNCTION__);
        throw new InvalidConfigurationException($message);
    }

    /**
     * @param string $service
     * @param string $method
     * @param array  $params
     * @param string $resultResultType
     *
     * @throws InvalidConfigurationException
     *
     * @return mixed
     */
    public function send($service, $method, array $params = [], $resultResultType = null)
    {
        $rpcProtocol = $this->protocol;
        $rpcRequest = $this->getRequestFactory()->create($rpcProtocol);

        if ($this->synchronous) {
            $rpcId = bin2hex(openssl_random_pseudo_bytes(16));
            $rpcRequest->setId($rpcId);
        }

        $rpcRequest->setMethod($service.':'.$method);
        if ($rpcRequest instanceof RpcXRequest) {
            $rpcRequest->setService($service);
            $rpcRequest->setMethod($method);
        }

        $rpcRequest->setParams($params);
        $rpcResponse = $this->sendRequest($rpcRequest, $resultResultType);

        $result = null;
        if ($rpcResponse instanceof RpcResponse) {
            $result = $rpcResponse->getResult();
        }

        return $result;
    }

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
}

<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Connection;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use MS\RpcBundle\Model\Rpc\Interfaces\Request as RpcRequestInterface;
use MS\RpcBundle\Model\Rpc\Interfaces\Response as RpcResponseInterface;

class HttpConnection extends AbstractConnection
{
    /** @var Client  */
    protected $client;

    /**
     * @param array $context
     * @param array $endpoint
     */
    public function __construct(array $context = [], array $endpoint = [])
    {
        parent::__construct($context, $endpoint);

        $this->client = new Client($this->endpoint);
    }

    /**
     * @param array $endpoint
     *
     * @return array
     */
    protected function prepareEndpoint(array $endpoint = [])
    {
        return $endpoint;
    }

    /**
     * @param RpcRequestInterface $rpcRequest
     *
     * @return Request
     */
    protected function convertRequest(RpcRequestInterface $rpcRequest)
    {
        $method = 'POST';
        $uri = $this->endpoint['base_uri'];
        $contentType = sprintf('application/%s+%s', $this->protocol, $this->encoding);
        $headers = [
            'Content-Type' => $contentType,
            'Accept' => $contentType,
        ];

        $format = $this->protocol;
        $context = ['encoding' => $this->encoding];
        $body = $this->serializer->serialize($rpcRequest, $format, $context);

        return new Request($method, $uri, $headers, $body);
    }

    /**
     * @param Response $response
     *
     * @return RpcResponseInterface
     */
    protected function convertResponse(Response $response)
    {
        $type = $this->responseFactory->className($this->protocol);
        $format = $this->protocol;
        $context = ['encoding' => $this->encoding];
        $body = $response->getBody();

        /** @var RpcResponseInterface $rpcResponse */
        $rpcResponse = $this->serializer->deserialize($body, $type, $format, $context);

        $rpcResult = $rpcResponse->getResult();
        $rpcResult = $this->serializer->denormalize($rpcResult, null, $format, $context);
        $rpcResponse->setResult($rpcResult);

        return $rpcResponse;
    }

    /**
     * @param RpcRequestInterface $rpcRequest
     *
     * @return RpcResponseInterface|null
     */
    protected function sendRequest(RpcRequestInterface $rpcRequest)
    {
        try {
            $request = $this->convertRequest($rpcRequest);

            if (!$this->synchronous) {
                $this->client->sendAsync($request);

                return;
            }

            $response = $this->client->send($request);
        } catch (ServerException $ex) {
            $response = $ex->getResponse();
        } catch (ClientException $ex) {
            $response = $ex->getResponse();
        }

        $rpcResponse = $this->convertResponse($response);

        return $rpcResponse;
    }
}

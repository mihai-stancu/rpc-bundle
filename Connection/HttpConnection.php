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
use MS\RpcBundle\Model\RpcRequest;
use MS\RpcBundle\Model\RpcResponse;

class HttpConnection extends AbstractConnection
{
    /** @var Client  */
    protected $client;

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
        parent::__construct($protocol, $encoding, $synchronous, $endpoint);

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
     * @param RpcRequest $rpcRequest
     *
     * @return Request
     */
    protected function convertRequest(RpcRequest $rpcRequest)
    {
        $method = 'POST';
        $uri = $this->endpoint['base_uri'];
        $headers = [
            'Accept' => 'application/'.$this->encoding,
            'Content-Type' => 'application/'.$this->encoding,
            'RPC-Request-Type' => $this->protocol,
            'RPC-Response-Type' => $this->protocol,
        ];

        $format = $this->protocol;
        $context = ['encoding' => $this->encoding];
        $body = $this->serializer->serialize($rpcRequest, $format, $context);

        return new Request($method, $uri, $headers, $body);
    }

    /**
     * @param Response $response
     * @param string   $resultType
     *
     * @return RpcResponse
     */
    protected function convertResponse(Response $response, $resultType)
    {
        $_type = $this->responseFactory->className($this->protocol);
        $format = $this->protocol;
        $context = ['encoding' => $this->encoding];
        $body = $response->getBody();

        /** @var RpcResponse $rpcResponse */
        $rpcResponse = $this->serializer->deserialize($body, $_type, $format, $context);

        $rpcResult = $rpcResponse->getResult();
        $rpcResult = $this->serializer->denormalize($rpcResult, $resultType, $format, $context);
        $rpcResponse->setResult($rpcResult);
    }

    /**
     * @param RpcRequest $rpcRequest
     * @param string     $resultType
     *
     * @return RpcResponse|null
     */
    protected function sendRequest(RpcRequest $rpcRequest, $resultType = null)
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

        $rpcResponse = $this->convertResponse($response, $resultType);

        return $rpcResponse;
    }
}

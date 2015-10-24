<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Connection;

use MS\RpcBundle\Model\RpcRequest;
use MS\RpcBundle\Model\RpcResponse;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class ZmqConnection extends QueueConnection
{
    /** @var \ZMQSocket */
    protected $socket;

    /** @var int  */
    protected $mode = \ZMQ::MODE_DONTWAIT;

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

        list($type, $dsn, $force, $mode) = array_values($this->endpoint);

        $this->context = new \ZMQContext();

        $this->socket = new \ZMQSocket($this->context, $type);
        $this->socket->bind($dsn, $force);

        $this->mode = $mode;
    }

    /**
     * @param array $endpoint
     *
     * @throws InvalidConfigurationException
     *
     * @return array
     */
    protected function prepareEndpoint(array $endpoint = [])
    {
        $required = ['type', 'dsn'];
        $defaults = [
            'type' => null,
            'dsn' => null,
            'force' => false,
            'mode' => null,
        ];

        $endpoint = array_merge($defaults, $endpoint);

        foreach ($required as $req) {
            if (!isset($endpoint[$req])) {
                $message = sprintf('Connection endpoint is not correctly defined: The "%s" is missing.', $req);
                throw new InvalidConfigurationException($message);
            }
        }

        return $endpoint;
    }

    /**
     * @param RpcRequest $rpcRequest
     *
     * @return RpcResponse|null
     */
    protected function sendRequest(RpcRequest $rpcRequest)
    {
        $mode = $this->mode;

        $message = $this->serializer->serialize(
            $rpcRequest,
            $this->protocol,
            ['encoding' => $this->encoding]
        );

        $this->socket->send($message, $mode);
    }
}

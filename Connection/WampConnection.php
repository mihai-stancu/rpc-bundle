<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Connection;

use Ratchet\Wamp\WampConnection as RatchetWampConnection;

class WampConnection extends AbstractConnection
{
    /** @var \ZMQSocket */
    protected $socket;

    /** @var int  */
    protected $mode = \ZMQ::MODE_DONTWAIT;

    /**
     * @param array $context
     * @param array $endpoint
     */
    public function __construct(array $context = [], array $endpoint = [])
    {
        parent::__construct($context, $endpoint);

        list($type, $dsn, $force, $mode) = array_values($this->endpoint);

        $this->context = new RatchetWampConnection();
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
     * @param Request $rpcRequest
     *
     * @return Response|null
     */
    protected function sendRequest(Request $rpcRequest)
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

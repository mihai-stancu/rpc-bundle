<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Connection;

use MS\RpcBundle\Model\Rpc\Request;
use MS\RpcBundle\Model\Rpc\Response;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class AmqpConnection extends QueueConnection
{
    /** @var \AMQPConnection */
    protected $connection;

    /** @var \AMQPChannel */
    protected $channel;

    /** @var \AMQPExchange */
    protected $exchange;

    /** @var  string */
    protected $routingKey;

    /** @var int  */
    protected $flags = AMQP_NOPARAM;

    /**
     * @param array $context
     * @param array $endpoint
     */
    public function __construct(array $context = [], array $endpoint = [])
    {
        parent::__construct($context, $endpoint);

        list($exchangeName, $routingKey) = array_values($this->endpoint);

        $credentials = array_filter($this->endpoint);

        $this->connection = new \AMQPConnection($credentials);
        $this->connection->connect();

        $this->channel = new \AMQPChannel($this->connection);

        $this->exchange = new \AMQPExchange($this->channel);
        $this->exchange->setName($exchangeName);
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
        $required = ['exchange'];
        $defaults = [
            'exchange' => null,
            'routing_key' => null,
            'host' => 'localhost',
            'port' => 5672,
            'login' => null,
            'password' => null,
            'vhost' => null,
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
        $contentType = 'application/'.$this->protocol.'+'.$this->encoding;
        $routingKey = $this->routingKey;
        $flags = $this->flags;
        $attributes = ['content-type' => $contentType];

        $message = $this->serializer->serialize(
            $rpcRequest,
            $this->protocol,
            ['encoding' => $this->encoding]
        );

        $this->exchange->publish($message, $routingKey, $flags, $attributes);
    }
}

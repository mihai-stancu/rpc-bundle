<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Connection;

use MS\RpcBundle\Model\Rpc\Request as RpcRequest;
use MS\RpcBundle\Model\Rpc\Response as RpcResponse;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class StompConnection extends QueueConnection
{
    /** @var \Stomp */
    protected $client;

    /** @var string */
    protected $destination;

    /** @var  string */
    protected $routingKey;

    /** @var int  */
    protected $flags = AMQP_NOPARAM;

    /**
     * @param string $protocol
     * @param string $encoding
     * @param bool   $synchronous
     * @param array  $endpoint
     */
    public function __construct(
        $protocol,
        $encoding,
        array $endpoint = [],
        $synchronous = false
    ) {
        parent::__construct($protocol, $encoding, $synchronous, $endpoint);

        list($broker, $username, $password, $headers, $destination) = array_values($this->endpoint);

        $this->client = new \Stomp($broker, $username, $password, $headers);
        $this->destination = $destination;
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
        $required = ['broker', 'destination'];
        $defaults = [
            'broker' => ini_get('stomp.default_broker_uri'),
            'username' => null,
            'password' => null,
            'headers' => [],
            'destination' => null,
        ];

        $endpoint = array_merge($defaults, $endpoint);

        foreach ($required as $req) {
            if (!isset($endpoint[$req])) {
                $message = sprintf('Connection endpoint is not correctly defined: The "%s" is missing.', $req);
                throw new InvalidConfigurationException($message);
            }
        }

        if (is_array($endpoint['destination'])) {
            $destination = reset($endpoint['destination']);
            $endpoint['destination'] = array_shift($endpoint['destination']);
            $endpoint['destination'] = vsprintf($destination, $endpoint['destination']);
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
        $destination = $this->destination;

        $message = $this->serializer->serialize(
            $rpcRequest,
            $this->protocol,
            ['encoding' => $this->encoding]
        );

        $headers = [
            'content_type' => 'application/'.$this->encoding,
            'rpc_type' => $this->protocol,
        ];

        $this->client->send($destination, $message, $headers);

        return;
    }
}

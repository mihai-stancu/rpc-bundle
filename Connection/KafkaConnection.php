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

class KafkaConnection extends QueueConnection
{
    /** @var \Kafka */
    protected $client;

    /** @var string */
    protected $topic;

    /**
     * @param array $context
     * @param array $endpoint
     */
    public function __construct(array $context = [], array $endpoint = [])
    {
        parent::__construct($context, $endpoint);

        list($brokers, $username, $password, $headers, $destination) = array_values($this->endpoint);

        $this->client = new \Kafka($brokers, $username, $password, $headers);
        $this->topic = $destination;
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
        $required = ['brokers', 'topic'];
        $defaults = [
            'brokers' => null,
            'headers' => [],
            'topic' => null,
        ];

        $endpoint = array_merge($defaults, $endpoint);

        foreach ($required as $req) {
            if (!isset($endpoint[$req])) {
                $message = sprintf('Connection endpoint is not correctly defined: The "%s" is missing.', $req);
                throw new InvalidConfigurationException($message);
            }
        }

        if (is_array($endpoint['topic'])) {
            $destination = reset($endpoint['topic']);
            $endpoint['topic'] = array_shift($endpoint['topic']);
            $endpoint['topic'] = vsprintf($destination, $endpoint['topic']);
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
        $topic = $this->topic;
        $contentType = sprintf('application/%s+%s', $this->protocol, $this->encoding);
        $headers = ['content-type' => $contentType];

        $message = $this->serializer->serialize(
            $rpcRequest,
            $this->protocol,
            ['encoding' => $this->encoding]
        );

        $this->client->produce($topic, $message);

        return;
    }
}

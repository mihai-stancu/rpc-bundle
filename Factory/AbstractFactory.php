<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Factory;

use MS\RpcBundle\Model\JsonRpcRequest;
use MS\RpcBundle\Model\JsonRpcResponse;
use MS\RpcBundle\Model\JsonRpcXRequest;
use MS\RpcBundle\Model\JsonRpcXResponse;
use MS\RpcBundle\Model\JsonRpcXSRequest;
use MS\RpcBundle\Model\JsonRpcXSResponse;
use MS\RpcBundle\Model\XmlRpcRequest;
use MS\RpcBundle\Model\XmlRpcResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;

abstract class AbstractFactory
{
    /**
     * @var array|string[]
     */
    protected static $protocols = [
        'request' => [
            'json-rpc' => JsonRpcRequest::class,
            'json-rpc-x' => JsonRpcXRequest::class,
            'json-rpc-xs' => JsonRpcXSRequest::class,
            'xml-rpc' => XmlRpcRequest::class,
        ],
        'response' => [
            'json-rpc' => JsonRpcResponse::class,
            'json-rpc-x' => JsonRpcXResponse::class,
            'json-rpc-xs' => JsonRpcXSResponse::class,
            'xml-rpc' => XmlRpcResponse::class,
        ],
    ];

    protected static $encodings = [
        'bencode',
        'bson',
        'cbor',
        'export',
        'igbinary',
        'json',
        'msgpack',
        'serialize',
        'tnetstring',
        'ubjson',
        'xml',
        'yaml',
    ];

    /** @var  Serializer */
    protected $serializer;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param Request|Response $object
     *
     * @return bool
     */
    public function validate($object)
    {
        $protocol = $object->headers->get('RPC-Request-Type');
        $encoding = $object->headers->get('Content-Type');
        $encoding = preg_replace('/^([^\/]*\/)/', '', $encoding);

        return in_array($encoding, static::$encodings)
           and array_key_exists($protocol, static::$protocols['request'])
           and array_key_exists($protocol, static::$protocols['response']);
    }
}

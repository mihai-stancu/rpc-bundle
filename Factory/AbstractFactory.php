<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Factory;

use MS\RpcBundle\Model\JSend\Request as JSendRequest;
use MS\RpcBundle\Model\JSend\Response as JSendResponse;
use MS\RpcBundle\Model\JsonRpc\Request as JsonRpcRequest;
use MS\RpcBundle\Model\JsonRpc\Response as JsonRpcResponse;
use MS\RpcBundle\Model\JsonRpcX\Request as JsonRpcXRequest;
use MS\RpcBundle\Model\JsonRpcX\Response as JsonRpcXResponse;
use MS\RpcBundle\Model\JsonRpcXS\Request as JsonRpcXSRequest;
use MS\RpcBundle\Model\JsonRpcXS\Response as JsonRpcXSResponse;
use MS\RpcBundle\Model\Rest\Request as RestRequest;
use MS\RpcBundle\Model\Rest\Response as RestResponse;
use MS\RpcBundle\Model\Rpc\Request as RpcRequest;
use MS\RpcBundle\Model\Rpc\Response as RpcResponse;
use MS\RpcBundle\Model\RpcX\Response as RpcXResponse;
use MS\RpcBundle\Model\Soap\Request as SoapRequest;
use MS\RpcBundle\Model\Soap\Response as SoapResponse;
use MS\RpcBundle\Model\XmlRpc\Request as XmlRpcRequest;
use MS\RpcBundle\Model\XmlRpc\Response as XmlRpcResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;

abstract class AbstractFactory
{
    const REGEX_CONTENT_TYPE = '/^((app)|(application))\/((x-www-(form)-urlencoded|(\w*+)))$/';

    /**
     * @var array|string[]
     */
    protected static $protocols = [
        'request' => [
            'jsend' => JSendRequest::class,
            'json-rpc' => JsonRpcRequest::class,
            'json-rpc-x' => JsonRpcXRequest::class,
            'json-rpc-xs' => JsonRpcXSRequest::class,
            'rest' => RestRequest::class,
            'rpc' => RpcRequest::class,
            'rpc-x' => JsonRpcRequest::class,
            'soap' => SoapRequest::class,
            'xml-rpc' => XmlRpcRequest::class,
        ],
        'response' => [
            'jsend' => JSendResponse::class,
            'json-rpc' => JsonRpcResponse::class,
            'json-rpc-x' => JsonRpcXResponse::class,
            'json-rpc-xs' => JsonRpcXSResponse::class,
            'rest' => RestResponse::class,
            'rpc' => RpcResponse::class,
            'rpc-x' => RpcXResponse::class,
            'soap' => SoapResponse::class,
            'xml-rpc' => XmlRpcResponse::class,
        ],
    ];

    protected static $encodings = [
        'bencode',
        'bson',
        'cbor',
        'export',
        'form',
        'igbinary',
        'json',
        'msgpack',
        'rison',
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
        $encoding = preg_replace(static::REGEX_CONTENT_TYPE, '$6$7', $encoding);

        return in_array($encoding, static::$encodings)
           and array_key_exists($protocol, static::$protocols['request'])
           and array_key_exists($protocol, static::$protocols['response']);
    }
}

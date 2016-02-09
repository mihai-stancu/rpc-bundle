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
use MS\RpcBundle\Model\JsonWsp\Request as JsonWspRequest;
use MS\RpcBundle\Model\JsonWsp\Response as JsonWspResponse;
use MS\RpcBundle\Model\Rest\Request as RestRequest;
use MS\RpcBundle\Model\Rest\Response as RestResponse;
use MS\RpcBundle\Model\Rpc\Request as RpcRequest;
use MS\RpcBundle\Model\Rpc\Response as RpcResponse;
use MS\RpcBundle\Model\RpcX\Request as RpcXRequest;
use MS\RpcBundle\Model\RpcX\Response as RpcXResponse;
use MS\RpcBundle\Model\Soap\Request as SoapRequest;
use MS\RpcBundle\Model\Soap\Response as SoapResponse;
use MS\RpcBundle\Model\XmlRpc\Request as XmlRpcRequest;
use MS\RpcBundle\Model\XmlRpc\Response as XmlRpcResponse;
use Symfony\Component\Serializer\Serializer;

abstract class AbstractFactory
{
    const REGEX_CONTENT_TYPE = '
        /^
            (?:app|application)
            \/
            (?:
                (?:
                    (?<protocol>[\w-]*+)
                    \+
                )?
                (?:x-www-)?(?P<encoding>form|[\w-]++)(?:-urlencoded)?
            )
        $/x
    ';

    /**
     * @var array|string[]
     */
    protected static $protocols = [
        'request' => [
            'jsend' => JSendRequest::class,
            'json-rpc' => JsonRpcRequest::class,
            'json-rpc-x' => JsonRpcXRequest::class,
            'json-rpc-xs' => JsonRpcXSRequest::class,
            'json-wsp' => JsonWspRequest::class,
            'rest' => RestRequest::class,
            'rpc' => RpcRequest::class,
            'rpc-x' => RpcXRequest::class,
            'soap' => SoapRequest::class,
            'xml-rpc' => XmlRpcRequest::class,
        ],
        'response' => [
            'jsend' => JSendResponse::class,
            'json-rpc' => JsonRpcResponse::class,
            'json-rpc-x' => JsonRpcXResponse::class,
            'json-rpc-xs' => JsonRpcXSResponse::class,
            'json-wsp' => JsonWspResponse::class,
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

    abstract public function className($protocol);

    /**
     * @param string $type
     *
     * @return bool
     */
    public function validate($type)
    {
        list($protocol, $encoding) = $this->getContentType($type);

        return in_array($encoding, static::$encodings)
           and array_key_exists($protocol, static::$protocols['request'])
           and array_key_exists($protocol, static::$protocols['response']);
    }

    /**
     * @param string $type
     *
     * @return array|bool
     */
    public function getContentType($type)
    {
        preg_match(static::REGEX_CONTENT_TYPE, $type, $matches);

        if (!isset($matches['protocol'], $matches['encoding'])) {
            return [null, null];
        }

        return [$matches['protocol'], $matches['encoding']];
    }

    /**
     * @param string $accept
     *
     * @return string
     */
    public function getAcceptType($accept)
    {
        if (strpos($accept, '*') === false) {
            return $accept;
        }

        $regex = str_replace('*', '[\w-]++', $accept);
        foreach (static::$protocols as $protocol) {
            foreach (static::$encodings as $encoding) {
                $candidate = sprintf(
                    'application/%s+%s',
                    $protocol,
                    $encoding
                );

                if ($accept === $candidate
                 or preg_match($regex, $candidate) > 0) {
                    return $candidate;
                }
            }
        }
    }
}

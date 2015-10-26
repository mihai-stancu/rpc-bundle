<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Serializer\Normalizer;

use MS\RpcBundle\Model\JsonRpcRequest;
use MS\RpcBundle\Model\JsonRpcResponse;
use MS\RpcBundle\Model\JsonRpcXRequest;
use MS\RpcBundle\Model\JsonRpcXResponse;
use MS\RpcBundle\Model\RpcAuth;
use MS\RpcBundle\Model\RpcError;
use MS\SerializerBundle\Serializer\Normalizer\TypehintNormalizer;
use Symfony\Component\Serializer\SerializerAwareInterface;

class RpcNormalizer extends TypehintNormalizer implements SerializerAwareInterface
{
    protected static $formats = [
        'json-rpc' => [
            RpcAuth::class,
            RpcError::class,
            JsonRpcRequest::class,
            JsonRpcResponse::class,
        ],
        'json-rpc-x' => [
            RpcAuth::class,
            RpcError::class,
            JsonRpcXRequest::class,
            JsonRpcXResponse::class,
        ],
    ];

    protected $ignoredAttributes = [
        'version',
    ];

    /**
     * @param mixed  $data
     * @param string $type
     * @param string $format
     *
     * @return bool
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return array_key_exists($format, static::$formats) and in_array($type, static::$formats[$format], true);
    }

    /**
     * @param object $data
     * @param string $format
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return is_object($data) and array_key_exists($format, static::$formats)
           and in_array(get_class($data), static::$formats[$format], true);
    }
}

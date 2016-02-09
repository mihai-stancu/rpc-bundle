<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Serializer\Normalizer;

use MS\RpcBundle\Model\JsonRpc\Request as JsonRpcRequest;
use MS\RpcBundle\Model\JsonRpc\Response as JsonRpcResponse;
use MS\RpcBundle\Model\JsonRpcX\Request as JsonRpcXRequest;
use MS\RpcBundle\Model\JsonRpcX\Response as JsonRpcXResponse;
use MS\RpcBundle\Model\Rpc\Error;
use MS\RpcBundle\Model\Rpc\Request as RpcRequest;
use MS\RpcBundle\Model\Rpc\Response as RpcResponse;
use MS\RpcBundle\Model\RpcX\Auth;
use MS\RpcBundle\Model\RpcX\Request as RpcXRequest;
use MS\RpcBundle\Model\RpcX\Response as RpcXResponse;
use MS\SerializerBundle\Serializer\Normalizer\MixedArrayDenormalizer;
use MS\SerializerBundle\Serializer\Normalizer\TypehintNormalizer;
use Symfony\Component\Serializer\SerializerAwareInterface;

class RpcNormalizer extends TypehintNormalizer implements SerializerAwareInterface
{
    const KEY_PARAMS = 'params';
    const KEY_RESULT = 'result';

    protected static $formats = [
        'json-rpc' => [
            Error::class,
            JsonRpcRequest::class,
            JsonRpcResponse::class,
        ],
        'json-rpc-x' => [
            Auth::class,
            Error::class,
            JsonRpcXRequest::class,
            JsonRpcXResponse::class,
        ],
        'rpc' => [
            Error::class,
            RpcRequest::class,
            RpcResponse::class,
        ],
        'rpc-x' => [
            Auth::class,
            Error::class,
            RpcXRequest::class,
            RpcXResponse::class,
        ],
    ];

    protected $ignoredAttributes = [
        'version',
    ];

    /**
     * @param array  $data
     * @param string $class
     * @param null   $format
     * @param array  $context
     *
     * @return object
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        foreach ([static::KEY_PARAMS, static::KEY_RESULT] as $key) {
            if (empty($data[$key])) {
                continue;
            }

            $class = isset($context['params']) ? $context['params'] : MixedArrayDenormalizer::TYPE;

            $data[$key] = $this->serializer->denormalize($data[$key], $class, $context['encoding'], $context);
        }

        return parent::denormalize($data, $class, $format, $context);
    }

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

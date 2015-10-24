<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Serializer\Normalizer;

use MS\RpcBundle\Model\JsonRpcXSRequest;
use MS\RpcBundle\Model\JsonRpcXSResponse;
use MS\RpcBundle\Model\RpcAuth;
use MS\RpcBundle\Model\RpcError;
use MS\RpcBundle\Model\RpcRequest;
use MS\RpcBundle\Model\RpcResponse;

class JsonRpcXSNormalizer extends RpcNormalizer
{
    const KEY_CONTROL = 'c';

    protected static $formats = [
        'json-rpc-xs' => [
            RpcAuth::class,
            RpcError::class,
            JsonRpcXSRequest::class,
            JsonRpcXSResponse::class,
        ],
    ];

    protected $ignoredAttributes = [
        'jsonrpc',
        'version',
        'service',
        'control',
        'auth',
        'method',
        'params',
        'result',
        'error',
        'id',
    ];

    /**
     * @param array  $data
     * @param string $class
     * @param string $format
     * @param array  $context
     *
     * @return RpcRequest|RpcResponse
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (!empty($data[static::KEY_CONTROL])) {
            $control = [];

            if (in_array('s', $data[static::KEY_CONTROL], true)) {
                $control[] = 'short';
            }
            if (in_array('g', $data[static::KEY_CONTROL], true)) {
                $control[] = 'gzcompress';
            }
            if (in_array('b', $data[static::KEY_CONTROL], true)) {
                $control[] = 'bzcompress';
            }

            $data[static::KEY_CONTROL] = $control;
        }

        $object = parent::denormalize($data, $class, $format, $context);

        return $object;
    }

    /**
     * @param mixed  $object
     * @param string $format
     * @param array  $context
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $array = parent::normalize($object, $format, $context);

        if ($object instanceof RpcRequest and isset($array[static::KEY_CONTROL])) {
            $control = [];

            if (in_array('short', $array[static::KEY_CONTROL], true)) {
                $control[] = 's';
            }
            if (in_array('gzcompress', $array[static::KEY_CONTROL], true)) {
                $control[] = 'g';
            }
            if (in_array('bzcompress', $array[static::KEY_CONTROL], true)) {
                $control[] = 'b';
            }

            $array[static::KEY_CONTROL] = $control;
        }

        return $array;
    }
}
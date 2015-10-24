<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Serializer\Normalizer;

use MS\RpcBundle\Model\RpcAuth;
use MS\RpcBundle\Model\RpcError;
use MS\RpcBundle\Model\XmlRpcRequest;
use MS\RpcBundle\Model\XmlRpcResponse;

class XmlRpcNormalizer extends RpcNormalizer
{
    protected static $formats = [
        'xml-rpc' => [
            RpcAuth::class,
            RpcError::class,
            XmlRpcRequest::class,
            XmlRpcResponse::class,
        ],
    ];

    protected $ignoredAttributes = [
        'method',
        'result',
        'error',
    ];
}

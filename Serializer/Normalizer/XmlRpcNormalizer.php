<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Serializer\Normalizer;

use MS\RpcBundle\Model\Rpc\Auth;
use MS\RpcBundle\Model\XmlRpc\Fault;
use MS\RpcBundle\Model\XmlRpc\Request;
use MS\RpcBundle\Model\XmlRpc\Response;

class XmlRpcNormalizer extends RpcNormalizer
{
    protected static $formats = [
        'xml-rpc' => [
            Auth::class,
            Fault::class,
            Request::class,
            Response::class,
        ],
    ];

    protected $ignoredAttributes = [
        'version',
        'method',
        'result',
        'error',
    ];
}

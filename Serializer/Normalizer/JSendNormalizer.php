<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Serializer\Normalizer;

use MS\RpcBundle\Model\JSend\Request;
use MS\RpcBundle\Model\JSend\Response;
use MS\RpcBundle\Model\Rpc\Auth;
use MS\RpcBundle\Model\Rpc\Error;

class JSendNormalizer extends RpcNormalizer
{
    protected static $formats = [
        'jsend' => [
            Auth::class,
            Error::class,
            Request::class,
            Response::class,
        ],
    ];

    protected $ignoredAttributes = [
        'version',
        'method',
        'result',

        'resource',
    ];

    /**
     * @param Request|Response $object
     * @param string           $format
     * @param array            $context
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $data = parent::normalize($object, $format, $context);

        return $data;
    }
}

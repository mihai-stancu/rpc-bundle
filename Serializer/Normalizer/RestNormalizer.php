<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Serializer\Normalizer;

use MS\RpcBundle\Model\Rest\Request;
use MS\RpcBundle\Model\Rest\Response;
use MS\RpcBundle\Model\Rpc\Error;

class RestNormalizer extends RpcNormalizer
{
    const KEY_PARAMS = 'params';
    const KEY_RESULT = 'data';

    protected static $formats = [
        'rest' => [
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

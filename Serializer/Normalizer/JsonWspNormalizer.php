<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Serializer\Normalizer;

use MS\RpcBundle\Model\JsonWsp\Fault;
use MS\RpcBundle\Model\JsonWsp\Request;
use MS\RpcBundle\Model\JsonWsp\Response;

class JsonWspNormalizer extends RpcNormalizer
{
    const KEY_PARAMS = 'args';
    const KEY_RESULT = 'result';

    protected static $formats = [
        'json-wsp' => [
            Fault::class,
            Request::class,
            Response::class,
        ],
    ];

    protected $ignoredAttributes = [
        'service',
        'method',
        'params',
        'status',
        'error',
        'result',
        'id',
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

<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Serializer\Normalizer;

use MS\RpcBundle\Model\Rpc\Auth;
use MS\RpcBundle\Model\Soap\Fault;
use MS\RpcBundle\Model\Soap\Request;
use MS\RpcBundle\Model\Soap\Response;

class SoapNormalizer extends RpcNormalizer
{
    protected static $formats = [
        'soap' => [
            Auth::class,
            Fault::class,
            Request::class,
            Response::class,
        ],
    ];

    protected $ignoredAttributes = [
        'version',
        'control',
        'auth',
        'service',
        'method',
        'params',
        'result',
        'status',
        'error',
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

        if (isset($data['body'])) {
            foreach ($data['body'] as $i => $param) {
                $params[$i] = $param = [
                '@xsi:type' => 'xsd:'.gettype($param),
                $param,
            ];
            }
        }

        return $data;
    }
}

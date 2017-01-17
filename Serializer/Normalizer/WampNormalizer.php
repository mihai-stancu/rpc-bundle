<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Serializer\Normalizer;

use MS\RpcBundle\Model\Rpc\Error;
use MS\RpcBundle\Model\Wamp\Request;
use MS\RpcBundle\Model\Wamp\Response;
use Thruway\Message\Message;

class WampNormalizer extends RpcNormalizer
{
    const KEY_PARAMS = 'arguments';
    const KEY_RESULT = 'result';

    protected static $formats = [
        'wamp' => [
            Error::class,
            Request::class,
            Response::class,
        ],
    ];

    protected $ignoredAttributes = [
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

        $code = Message::MSG_UNKNOWN;
        if ($object instanceof Request) {
            $code = Message::MSG_CALL;
        } elseif ($object instanceof Response) {
            if ($object->getError()) {
                $code = Message::MSG_ERROR;
            } else {
                $code = Message::MSG_RESULT;
            }
        }

        return [
            $code,
            $options,
            $object->getMethod(),
        ];
    }
}

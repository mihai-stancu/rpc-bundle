<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\JSend;

use MS\RpcBundle\Model\Rpc\Interfaces\Request as RpcRequestInterface;
use MS\RpcBundle\Model\Rpc\Traits\Message as RpcMessageTrait;
use MS\RpcBundle\Model\Rpc\Traits\Request as RpcRequestTrait;
use MS\SerializerBundle\Serializer\Normalizer\ParamsDenormalizer;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\Serializer\Serializer;

class Request implements RpcRequestInterface
{
    use RpcMessageTrait;
    use RpcRequestTrait;

    /**
     * @param HttpRequest $httpRequest
     * @param Serializer  $serializer
     * @param array       $context
     *
     * @return Request
     */
    public static function factory(HttpRequest $httpRequest, Serializer $serializer, array $context = [])
    {
        $attributes = $httpRequest->attributes;
        $content = $httpRequest->getContent();

        $method = $attributes->get('method');
        $params = $serializer->deserialize($content, ParamsDenormalizer::TYPE, 'rest', $context);
        $id = $attributes->get('id');

        $request = new static();
        $request->setMethod($method);
        $request->setParams($params);
        $request->setId($id);
    }
}

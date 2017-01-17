<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\Rest;

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
        $content = $httpRequest->getContent();

        $request = new static();
        $action = $httpRequest->getMethod();
        $request->setAction($action);
        $id = $httpRequest->getSession()->getId();
        $request->setId($id);

        $attributes = $httpRequest->attributes->all();
        if (!empty($attributes['_resource'])) {
            $params['resource'] = $attributes['_resource'];
            unset($attributes['_resource']);
        }
        foreach ($attributes as $key => $value) {
            if (isset($key[0]) and $key[0] === '_') {
                unset($attributes[$key]);
            }
        }

        /** @var array $params */
        $params = $serializer->deserialize($content, ParamsDenormalizer::TYPE, 'rest', $context);

        $query = $httpRequest->query->all();
        if (!empty($query)) {
            $params['query'] = array_merge($params['query'], $query);
        }

        $request->setParams($params);
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->getMethod();
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->setMethod($action);
    }
}

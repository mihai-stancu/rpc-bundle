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
        $attributes = $httpRequest->attributes;
        $content = $httpRequest->getContent();

        $action = $httpRequest->getMethod();
        $resource = $attributes->get('resource');
        $params = $serializer->deserialize($content, ParamsDenormalizer::TYPE, 'rest', $context);
        $id = $attributes->get('id');

        $request = new static();
        $request->setAction($action);
        $request->setResource($resource);
        $request->setParams($params);
        $request->setId($id);
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

    /**
     * @return int|string
     */
    public function getResource()
    {
        $params = $this->getParams();

        if (isset($params['resource'])) {
            return $params['resource'];
        }
    }

    /**
     * @param int|string $resource
     */
    public function setResource($resource)
    {
        $params = (array) $this->getParams();
        $params['resource'] = $resource;

        $this->setParams($params);
    }
}

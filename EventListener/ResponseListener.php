<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\EventListener;

use MS\RpcBundle\Factory\AbstractFactory;
use MS\RpcBundle\Factory\ResponseFactory;
use MS\RpcBundle\Model\Rpc\Request as RpcRequest;
use MS\RpcBundle\Model\Rpc\Response as RpcResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Serializer\SerializerInterface;

class ResponseListener
{
    /** @var ResponseFactory */
    protected $factory;

    /** @var  SerializerInterface */
    protected $serializer;

    public function __construct(ResponseFactory $factory, SerializerInterface $serializer)
    {
        $this->factory = $factory;
        $this->serializer = $serializer;
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        if (!$this->factory->validate($request)) {
            return;
        }

        $rpcRequest = $request->attributes->get('rpcRequest');
        $rpcResponse = $event->getControllerResult();

        $response = $this->getResponse($event, $request, $rpcRequest);
        $response->setStatusCode(Response::HTTP_OK);
        $this->setResponse($event, $response, $rpcResponse);
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $request = $event->getRequest();
        if (!$this->factory->validate($request)) {
            return;
        }

        $rpcRequest = $request->attributes->get('rpcRequest');
        $response = $this->getResponse($event, $request, $rpcRequest);
        $exception = $event->getException();
        $rpcResponse = $this->factory->createFrom($request, $rpcRequest, $exception);
        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        $this->setResponse($event, $response, $rpcResponse);
    }

    /**
     * @param GetResponseEvent $event
     * @param Request          $request
     * @param RpcRequest       $rpcRequest
     *
     * @return Response
     */
    protected function getResponse(GetResponseEvent $event, Request $request, RpcRequest $rpcRequest = null)
    {
        $response = $event->getResponse() ?: new Response();

        $acceptType = $request->headers->get('Accept');
        $contentType = $request->getContentType();
        if (!in_array($acceptType, ['*/*', $contentType])) {
            $contentType = $acceptType;
        }
        $response->headers->set('Content-Type', $contentType);

        $rpcProtocol = $request->headers->get('RPC-Response-Type');
        if ($rpcProtocol === null) {
            $rpcProtocol = $request->headers->get('RPC-Request-Type');
        }
        $response->headers->set('RPC-Response-Type', $rpcProtocol);

        return $response;
    }

    /**
     * @param GetResponseEvent $event
     * @param Response         $response
     * @param RpcResponse      $rpcResponse
     */
    protected function setResponse(GetResponseEvent $event, Response $response, RpcResponse $rpcResponse)
    {
        $protocol = $response->headers->get('RPC-Response-Type');
        $encoding = $response->headers->get('Content-Type');
        $encoding = preg_replace(AbstractFactory::REGEX_CONTENT_TYPE, '$6$7', $encoding);

        $content = $this->serializer->serialize($rpcResponse, $protocol, ['encoding' => $encoding]);
        $response->setContent($content);

        $event->setResponse($response);
    }
}

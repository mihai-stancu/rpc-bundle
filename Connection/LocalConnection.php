<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Connection;

use Symfony\Component\DependencyInjection\ContainerInterface;

class LocalConnection extends AbstractConnection
{
    /** @var  ContainerInterface */
    protected $container;

    /**
     * @param ContainerInterface $container
     * @param string             $protocol
     * @param string             $encoding
     * @param bool               $synchronous
     * @param array              $endpoint
     */
    public function __construct(
        ContainerInterface $container,
        $protocol,
        $encoding,
        $synchronous = true,
        array $endpoint = []
    ) {
        parent::__construct($protocol, $encoding, $synchronous, $endpoint);

        $this->container = $container;
    }

    /**
     * @param string $service
     * @param string $method
     * @param array  $params
     *
     * @return mixed
     */
    public function send($service, $method, array $params = [])
    {
        $service = $this->container->get($service);

        return call_user_func_array([$service, $method], $params);
    }
}

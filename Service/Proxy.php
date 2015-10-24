<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Service;

use MS\RpcBundle\Connection\ConnectionInterface;
use MS\RpcBundle\RpcException;

trait Proxy
{
    /** @var ConnectionInterface */
    protected $connection;

    /** @var  string */
    protected $service;

    /**
     * @param ConnectionInterface $connection
     * @param string              $service
     */
    public function __construct(ConnectionInterface $connection, $service)
    {
        $this->connection = $connection;
        $this->service = $service;
    }

    /**
     * @param string $method
     * @param array  $arguments
     * @param string $resultType
     *
     * @throws RpcException
     *
     * @return mixed
     */
    protected function call($method, $arguments, $resultType = null)
    {
        return $this->connection->send($this->service, $method, $arguments, $resultType);
    }
}

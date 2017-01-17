<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Proxy;

use MS\RpcBundle\Connection\Connection;
use MS\RpcBundle\RpcException;

trait Proxy
{
    /** @var Connection */
    protected $connection;

    /** @var  string */
    protected $service;

    /**
     * @param Connection $connection
     * @param string     $service
     */
    public function __construct(Connection $connection, $service)
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

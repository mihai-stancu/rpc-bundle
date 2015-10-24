<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Connection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

interface ConnectionInterface
{
    /**
     * @param string $service
     * @param string $method
     * @param array  $params
     * @param string $resultType
     *
     * @throws InvalidConfigurationException
     *
     * @return mixed
     */
    public function send($service, $method, array $params = [], $resultType = null);
}

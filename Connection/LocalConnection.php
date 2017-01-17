<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Connection;

class LocalConnection extends AbstractConnection
{
    /**
     * @param string $service
     * @param string $method
     * @param array  $params
     * @param null   $resultResultType
     *
     * @return mixed
     */
    public function send($service, $method, array $params = [], $resultResultType = null)
    {
        $service = $this->container->get($service);

        return call_user_func_array([$service, $method], $params);
    }
}

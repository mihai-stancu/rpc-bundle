<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\RpcX\Interfaces;

use MS\RpcBundle\Model\Rpc\Interfaces\Request as RpcRequest;

interface Request extends RpcRequest
{
    /**
     * @return array
     */
    public function getControl();

    /**
     * @param array $control
     */
    public function setControl($control);

    /**
     * @return Auth
     */
    public function getAuth();

    /**
     * @param Auth $auth
     */
    public function setAuth(Auth $auth = null);

    /**
     * @return object|string
     */
    public function getService();

    /**
     * @param object|string $service
     */
    public function setService($service);
}

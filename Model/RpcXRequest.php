<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model;

class RpcXRequest extends RpcRequest
{
    #region property control

    /**
     * @var array
     */
    protected $control = [];

    /**
     * @return array
     */
    public function getControl()
    {
        return $this->control;
    }

    /**
     * @param array $control
     */
    public function setControl($control)
    {
        $this->control = $control;
    }

    #endregion

    #region property auth

    /**
     * @var RpcAuth
     */
    protected $auth;

    /**
     * @return RpcAuth
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * @param RpcAuth $auth
     */
    public function setAuth(RpcAuth $auth = null)
    {
        $this->auth = $auth;
    }

    #endregion

    #region property service

    /**
     * @var string|object
     */
    protected $service;

    /**
     * @return object|string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param object|string $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }

    #endregion
}

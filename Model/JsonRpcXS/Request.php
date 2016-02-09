<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\JsonRpcXS;

use MS\RpcBundle\Model\RpcX\Interfaces\Auth;
use MS\RpcBundle\Model\RpcX\Interfaces\Request as RpcXRequestInterface;
use MS\RpcBundle\Model\RpcX\Traits\Request as RpcXRequestTrait;

class Request implements RpcXRequestInterface
{
    use Traits\Message;
    use RpcXRequestTrait;

    const VERSION = '2.0.xs';

    #region property control

    /**
     * @return array
     */
    public function getC()
    {
        return $this->getControl();
    }

    /**
     * @param array $c
     */
    public function setC($c)
    {
        $this->setControl($c);
    }

    #endregion

    #region property auth

    /**
     * @return Auth
     */
    public function getA()
    {
        return $this->getAuth();
    }

    /**
     * @param Auth $a
     */
    public function setA(Auth $a = null)
    {
        $this->setAuth($a);
    }

    #endregion

    #region property service

    /**
     * @return string
     */
    public function getS()
    {
        return $this->getService();
    }

    /**
     * @param string $s
     */
    public function setS($s)
    {
        $this->setService($s);
    }

    #endregion

    #region property method

    /**
     * @return string
     */
    public function getM()
    {
        return $this->getMethod();
    }

    /**
     * @param string $m
     */
    public function setM($m)
    {
        $this->setMethod($m);
    }

    #endregion

    #region property params

    /**
     * @return array
     */
    public function getP()
    {
        return $this->getParams();
    }

    /**
     * @param array $p
     */
    public function setP($p)
    {
        $this->setParams($p);
    }

    #endregion
}

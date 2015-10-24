<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model;

class JsonRpcXSResponse extends JsonRpcXResponse
{
    #region property jsonrpc

    protected $version = '2.0.xs';

    /**
     * @return string
     */
    public function getJ()
    {
        return $this->getVersion();
    }

    /**
     * @param string $j
     */
    public function setJ($j)
    {
        $this->setVersion($j);
    }

    #endregion

    #region property status

    /**
     * @return int|string
     */
    public function getS()
    {
        return $this->getStatus();
    }

    /**
     * @param int|string $s
     */
    public function setS($s)
    {
        $this->setStatus($s);
    }

    #endregion

    #region property result

    /**
     * @return object
     */
    public function getR()
    {
        return $this->getResult();
    }

    /**
     * @param object $r
     */
    public function setR($r)
    {
        $this->setResult($r);
    }

    #endregion

    #region property error

    /**
     * @return RpcError
     */
    public function getE()
    {
        return $this->getError();
    }

    /**
     * @param RpcError $e
     */
    public function setE(RpcError $e = null)
    {
        $this->setError($e);
    }

    #endregion

    #region property id

    /**
     * @return int|string
     */
    public function getI()
    {
        return $this->getId();
    }

    /**
     * @param int|string $i
     */
    public function setI($i)
    {
        $this->setId($i);
    }

    #endregion
}

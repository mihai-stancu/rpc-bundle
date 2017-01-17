<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\Rpc\Traits;

trait Request
{
    #region property method

    /**
     * @var string
     */
    protected $method;

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    #endregion

    #region property params

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @param mixed $value
     * @param string $key
     */
    public function addParam($value, $key = null)
    {
        if ($key !== null) {
            $this->params[$key] = $value;

            return;
        }

        $this->params[] = $value;
    }

    public function removeParam($key)
    {

    }

    #endregion
}

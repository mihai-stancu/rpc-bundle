<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model;

class XmlRpcRequest extends RpcRequest
{
    #region property method

    /**
     * @return string
     */
    public function getMethodName()
    {
        return $this->getMethod();
    }

    /**
     * @param string $methodName
     */
    public function setMethodName($methodName)
    {
        $this->setMethod($methodName);
    }

    #endregion

    #region property params

    /**
     * @return array
     */
    public function getParams()
    {
        return parent::getParams();
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        parent::setParams($params);
    }

    #endregion
}

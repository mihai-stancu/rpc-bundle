<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model;

class XmlRpcResponse extends RpcResponse
{
    #region property result

    /**
     * @return object
     */
    public function getParams()
    {
        return $this->getResult();
    }

    /**
     * @param object $params
     */
    public function setParams($params)
    {
        $this->setResult($params);
    }

    #endregion

    #region property error

    /**
     * @return RpcError
     */
    public function getFault()
    {
        return $this->getError();
    }

    /**
     * @param RpcError $fault
     */
    public function setFault(RpcError $fault = null)
    {
        $this->setError($fault);
    }

    #endregion
}

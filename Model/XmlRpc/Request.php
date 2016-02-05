<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\XmlRpc;

use MS\RpcBundle\Model\Rpc\Request as RpcRequest;

class Request extends RpcRequest
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
}

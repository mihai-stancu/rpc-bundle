<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\XmlRpc;

use MS\RpcBundle\Model\Rpc\Interfaces\Response as RpcResponseInterface;
use MS\RpcBundle\Model\Rpc\Traits\Message as RpcMessageTrait;
use MS\RpcBundle\Model\Rpc\Traits\Response as RpcResponseTrait;

class Response implements RpcResponseInterface
{
    use RpcMessageTrait;
    use RpcResponseTrait;

    #region property result

    /**
     * @return array|object
     */
    public function getParams()
    {
        return $this->getResult();
    }

    /**
     * @param array|object $params
     */
    public function setParams($params)
    {
        $this->setResult($params);
    }

    #endregion

    #region property error

    /**
     * @return Fault
     */
    public function getFault()
    {
        return $this->getError();
    }

    /**
     * @param Fault $fault
     */
    public function setFault(Fault $fault = null)
    {
        $this->setError($fault);
    }

    #endregion
}

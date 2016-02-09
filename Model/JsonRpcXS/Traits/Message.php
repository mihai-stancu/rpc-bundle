<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\JsonRpcXS\Traits;

use MS\RpcBundle\Model\RpcX\Traits\Message as RpcXMessageTrait;

trait Message
{
    use RpcXMessageTrait;

    #region property jsonrpc

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

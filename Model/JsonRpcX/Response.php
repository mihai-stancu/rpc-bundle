<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\JsonRpcX;

use MS\RpcBundle\Model\RpcX\Response as RpcXRequest;

class Response extends RpcXRequest
{
    #region property jsonrpc

    protected $version = '2.0.x';

    /**
     * @return string
     */
    public function getJsonrpc()
    {
        return $this->getVersion();
    }

    /**
     * @param string $jsonrpc
     */
    public function setJsonrpc($jsonrpc)
    {
        $this->setVersion($jsonrpc);
    }

    #endregion
}
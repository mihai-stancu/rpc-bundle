<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\JsonRpc\Traits;

use MS\RpcBundle\Model\Rpc\Traits\Message as RpcMessageTrait;

trait Message
{
    use RpcMessageTrait;

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
}

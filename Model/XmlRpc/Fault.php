<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\XmlRpc;

use MS\RpcBundle\Model\Rpc\Interfaces\Error as RpcErrorInterface;
use MS\RpcBundle\Model\Rpc\Traits\Error as RpcErrorTrait;

class Fault implements RpcErrorInterface
{
    use RpcErrorTrait;

    /**
     * @return string
     */
    public function getFaultMessage()
    {
        return $this->getMessage();
    }

    /**
     * @param $message
     */
    public function setFaultMessage($message)
    {
        $this->setMessage($message);
    }

    /**
     * @return int|string
     */
    public function getFaultCode()
    {
        return $this->getCode();
    }

    /**
     * @param int|string $code
     */
    public function setFaultCode($code)
    {
        $this->setCode($code);
    }
}

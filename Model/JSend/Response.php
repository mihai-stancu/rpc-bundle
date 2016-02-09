<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\JSend;

use MS\RpcBundle\Model\Rpc\Error;
use MS\RpcBundle\Model\Rpc\Interfaces\Response as RpcResponseInterface;
use MS\RpcBundle\Model\Rpc\Traits\Message as RpcMessageTrait;
use MS\RpcBundle\Model\Rpc\Traits\Response as RpcResponseTrait;

class Response implements RpcResponseInterface
{
    use RpcMessageTrait;
    use RpcResponseTrait;

    /**
     * @return array|object
     */
    public function getData()
    {
        return $this->getResult();
    }

    /**
     * @param array|object $data
     */
    public function setData($data)
    {
        $this->setResult($data);
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        if ($this->getError()) {
            return $this->getError()->getMessage();
        }
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        if (!$this->getError()) {
            $this->setError(new Error());
        }

        $this->getError()->setMessage($message);
    }
}

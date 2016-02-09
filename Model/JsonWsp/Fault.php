<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\JsonWsp;

use MS\RpcBundle\Model\RpcX\Interfaces\Error as RpcXErrorInterface;
use MS\RpcBundle\Model\RpcX\Traits\Error as RpcXErrorTrait;

class Fault implements RpcXErrorInterface
{
    use RpcXErrorTrait;

    /**
     * @return string
     */
    public function getString()
    {
        return $this->getMessage();
    }

    /**
     * @param string $string
     */
    public function setString($string)
    {
        $this->setMessage($string);
    }

    /**
     * @return object
     */
    public function getDetail()
    {
        return $this->getData();
    }

    /**
     * @param object $detail
     */
    public function setDetail($detail)
    {
        $this->setData($detail);
    }
}

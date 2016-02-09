<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\Rpc\Interfaces;

interface Error
{
    /**
     * @return int|string
     */
    public function getCode();

    /**
     * @param int|string $code
     */
    public function setCode($code);
    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param string $message
     */
    public function setMessage($message);

    /**
     * @return object
     */
    public function getData();

    /**
     * @param object $data
     */
    public function setData($data);
}

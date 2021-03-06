<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\Rpc\Interfaces;

interface Response extends Message
{
    /**
     * @return array
     */
    public function getResult();

    /**
     * @param object|array $result
     */
    public function setResult($result);

    /**
     * @return Error
     */
    public function getError();

    /**
     * @param Error $error
     */
    public function setError(Error $error);
}

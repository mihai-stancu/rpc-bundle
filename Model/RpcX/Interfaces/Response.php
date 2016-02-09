<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\RpcX\Interfaces;

use MS\RpcBundle\Model\Rpc\Interfaces\Response as RpcResponse;

interface Response extends RpcResponse
{
    /**
     * @return int|string
     */
    public function getStatus();

    /**
     * @param int|string $status
     */
    public function setStatus($status);
}

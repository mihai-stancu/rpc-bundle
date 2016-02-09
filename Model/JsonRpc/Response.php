<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\JsonRpc;

use MS\RpcBundle\Model\Rpc\Interfaces\Response as RpcResponseInterface;
use MS\RpcBundle\Model\Rpc\Traits\Message as RpcMessageTrait;
use MS\RpcBundle\Model\Rpc\Traits\Response as RpcResponseTrait;

class Response implements RpcResponseInterface
{
    use RpcMessageTrait;
    use RpcResponseTrait;

    const VERSION = '2.0';
}

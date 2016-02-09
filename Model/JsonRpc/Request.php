<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\JsonRpc;

use MS\RpcBundle\Model\JsonRpc\Traits\Message as JsonRpcMessageTrait;
use MS\RpcBundle\Model\Rpc\Interfaces\Request as RpcRequestInterface;
use MS\RpcBundle\Model\Rpc\Traits\Request as RpcRequestTrait;

class Request implements RpcRequestInterface
{
    use JsonRpcMessageTrait;
    use RpcRequestTrait;

    const VERSION = '2.0';
}

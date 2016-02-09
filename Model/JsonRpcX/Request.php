<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\JsonRpcX;

use MS\RpcBundle\Model\JsonRpcX\Traits\Message as JsonRpcXMessageTrait;
use MS\RpcBundle\Model\RpcX\Interfaces\Request as RpcXRequestInterface;
use MS\RpcBundle\Model\RpcX\Traits\Request as RpcXRequestTrait;

class Request implements RpcXRequestInterface
{
    use JsonRpcXMessageTrait;
    use RpcXRequestTrait;

    const VERSION = '2.0.x';
}

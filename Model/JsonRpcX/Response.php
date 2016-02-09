<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\JsonRpcX;

use MS\RpcBundle\Model\RpcX\Interfaces\Response as RpcXResponseInterface;
use MS\RpcBundle\Model\RpcX\Traits\Message as RpcXMessageTrait;
use MS\RpcBundle\Model\RpcX\Traits\Response as RpcXResponseTrait;

class Response implements RpcXResponseInterface
{
    use RpcXMessageTrait;
    use RpcXResponseTrait;

    const VERSION = '2.0.x';
}

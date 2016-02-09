<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\Rpc;

use MS\RpcBundle\Model\Rpc\Interfaces\Request as RequestInterface;

/**
 * RPC Request object.
 */
class Request implements RequestInterface
{
    use Traits\Message;
    use Traits\Request;
}

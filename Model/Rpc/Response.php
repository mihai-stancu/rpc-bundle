<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\Rpc;

use MS\RpcBundle\Model\Rpc\Interfaces\Response as ResponseInterface;

/**
 * RPC Response object.
 */
class Response implements ResponseInterface
{
    use Traits\Message;
    use Traits\Response;
}

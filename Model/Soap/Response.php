<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\Soap;

use MS\RpcBundle\Model\Rpc\Interfaces\Response as RpcResponseInterface;

class Response implements RpcResponseInterface
{
    use Traits\Envelope;

    const VERSION = 'http://www.w3.org/2003/05/soap-envelope';
}

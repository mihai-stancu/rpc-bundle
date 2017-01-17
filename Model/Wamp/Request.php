<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\Wamp;

use MS\RpcBundle\Model\Rpc\Interfaces\Request as RpcRequestInterface;
use MS\RpcBundle\Model\Rpc\Traits\Message as RpcMessageTrait;
use MS\RpcBundle\Model\Rpc\Traits\Request as RpcRequestTrait;

class Request implements RpcRequestInterface
{
    use RpcMessageTrait;
    use RpcRequestTrait;

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->getParams();
    }

    /**
     * @param array $arguments
     */
    public function setArguments($arguments)
    {
        $this->setParams($arguments);
    }
}

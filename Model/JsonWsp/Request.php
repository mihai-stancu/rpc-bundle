<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\JsonWsp;

use MS\RpcBundle\Model\Rpc\Interfaces\Request as RpcRequestInterface;
use MS\RpcBundle\Model\Rpc\Traits\Request as RpcRequestTrait;

class Request implements RpcRequestInterface
{
    use Traits\Message;
    use RpcRequestTrait {
        Traits\Message::getMethod insteadof RpcRequestTrait;
        Traits\Message::setMethod insteadof RpcRequestTrait;
    }

    const VERSION = '1.0';

    /**
     * @return array
     */
    public function getArgs()
    {
        return $this->getParams();
    }

    /**
     * @param array $params
     */
    public function setArgs($params)
    {
        $this->setParams($params);
    }
}

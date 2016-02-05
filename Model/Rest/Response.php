<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\Rest;

use MS\RpcBundle\Model\Rpc\Response as RpcResponse;

class Response extends RpcResponse
{
    /**
     * @return array|object
     */
    public function getData()
    {
        return $this->getResult();
    }

    /**
     * @param array|object $data
     */
    public function setData($data)
    {
        $this->setResult($data);
    }
}

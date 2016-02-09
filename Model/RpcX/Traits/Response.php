<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\RpcX\Traits;

use MS\RpcBundle\Model\Rpc\Traits\Response as RpcResponseTrait;

trait Response
{
    use RpcResponseTrait;

    #region property status

    /**
     * @var int|string
     */
    protected $status;

    /**
     * @return int|string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int|string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    #endregion
}

<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\JsonRpcXS;

use MS\RpcBundle\Model\Rpc\Interfaces\Error;
use MS\RpcBundle\Model\RpcX\Interfaces\Response as RpcXResponseInterface;
use MS\RpcBundle\Model\RpcX\Traits\Response as RpcXResponseTrait;

class Response implements RpcXResponseInterface
{
    use Traits\Message;
    use RpcXResponseTrait;

    const VERSION = '2.0.xs';

    #region property status

    /**
     * @return int|string
     */
    public function getS()
    {
        return $this->getStatus();
    }

    /**
     * @param int|string $s
     */
    public function setS($s)
    {
        $this->setStatus($s);
    }

    #endregion

    #region property result

    /**
     * @return object
     */
    public function getR()
    {
        return $this->getResult();
    }

    /**
     * @param object $r
     */
    public function setR($r)
    {
        $this->setResult($r);
    }

    #endregion

    #region property error

    /**
     * @return Error
     */
    public function getE()
    {
        return $this->getError();
    }

    /**
     * @param Error $e
     */
    public function setE(Error $e = null)
    {
        $this->setError($e);
    }

    #endregion
}

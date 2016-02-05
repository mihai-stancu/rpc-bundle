<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\Rpc;

/**
 * RPC Response object.
 */
class Response extends Message
{
    #region property result

    /**
     * @var array|object
     */
    protected $result;

    /**
     * @return array|object
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param array|object $result
     */
    public function setResult($result)
    {
        if (!empty($result)) {
            $this->error = null;
        }

        $this->result = $result;
    }

    #endregion

    #region property error

    /**
     * @var Error
     */
    protected $error;

    /**
     * @return Error
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param Error $error
     */
    public function setError(Error $error = null)
    {
        if (!empty($error)) {
            $this->result = null;
        }

        $this->error = $error;
    }

    #endregion
}

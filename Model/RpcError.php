<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model;

class RpcError
{
    #region property code

    /**
     * @var int|string
     */
    protected $code;

    /**
     * @return int|string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int|string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    #endregion

    #region property message

    /**
     * @var string
     */
    protected $message;

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    #endregion

    #region property data

    /**
     * @var object
     */
    protected $data;

    /**
     * @return object
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    #endregion
}

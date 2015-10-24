<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model;

/**
 * RPC Response object.
 */
class RpcResponse
{
    #region property version

    protected $version;

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    #endregion

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
     * @var RpcError
     */
    protected $error;

    /**
     * @return RpcError
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param RpcError $error
     */
    public function setError(RpcError $error = null)
    {
        if (!empty($error)) {
            $this->result = null;
        }

        $this->error = $error;
    }

    #endregion

    #region property id

    /**
     * @var int|string
     */
    protected $id;

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    #endregion
}

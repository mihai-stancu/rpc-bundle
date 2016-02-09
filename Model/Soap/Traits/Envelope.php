<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\Soap\Traits;

use MS\RpcBundle\Model\Rpc\Interfaces\Error;
use MS\RpcBundle\Model\Rpc\Traits\Message;
use MS\RpcBundle\Model\Soap\Body;
use MS\RpcBundle\Model\Soap\Header;

trait Envelope
{
    use Message;

    public function __construct()
    {
        $this->body = new Body();
    }

    #region accessors stoap

    /**
     * @return string
     */
    public function getSoap()
    {
        return $this->getVersion();
    }

    /**
     * @param string $soap
     */
    public function setSoap($soap)
    {
        $this->setVersion($soap);
    }

    #endregion

    #region property header

    /** @var  Header */
    protected $header;

    /**
     * @return Header
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param Header $header
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }

    #endregion

    #region property body

    /** @var  Body */
    protected $body;

    /**
     * @return Body
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param Body $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    #endregion

    #region accessors method

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->getBody()->getMethod();
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->getBody()->setMethod($method);
    }

    #endregion

    #region accessors params

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->getBody()->getParams();
    }

    /**
     * @param $params
     */
    public function setParams($params)
    {
        $this->getBody()->setParams($params);
    }

    #endregion

    #region accessors result

    /**
     * @return array|object
     */
    public function getResult()
    {
        return $this->getBody()->getResult();
    }

    /**
     * @param array|object $result
     */
    public function setResult($result)
    {
        $this->getBody()->setResult($result);
    }

    #endregion

    #region accessors error

    /**
     * @return Error
     */
    public function getError()
    {
        return $this->getBody()->getError();
    }

    /**
     * @param Error $error
     */
    public function setError(Error $error)
    {
        $this->getBody()->setError($error);
    }

    #endregion
}

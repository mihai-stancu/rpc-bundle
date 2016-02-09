<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\JsonWsp\Traits;

use MS\RpcBundle\Model\JsonWsp\Description;
use MS\RpcBundle\Model\JsonWsp\Request;
use MS\RpcBundle\Model\JsonWsp\Response;

trait Message
{
    #region property type

    /** @var  string */
    protected $type;

    /**
     * @return string
     */
    public function getType()
    {
        if ($this->type === null) {
            switch (true) {
                case ($this instanceof Response and $this->getFault()):
                    $this->type = 'jsonwsp/fault';
                    break;

                case ($this instanceof Response and $this->getResult()):
                    $this->type = 'jsonwsp/response';
                    break;

                case ($this instanceof Description):
                    $this->type = 'jsonwsp/description';
                    break;

                case ($this instanceof Request):
                    $this->type = 'jsonwsp/request';
                    break;
            }
        }

        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    #endregion

    #region property version

    protected $version;

    /**
     * @return string
     */
    public function getVersion()
    {
        if ($this->version === null and defined(static::class.'::VERSION')) {
            $this->version = constant(static::class.'::VERSION');
        }

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

    #region property servicename

    /** @var  string */
    protected $servicename;

    /**
     * @return object|string
     */
    public function getServicename()
    {
        return $this->servicename;
    }

    /**
     * @param object|string $servicename
     */
    public function setServicename($servicename)
    {
        $this->servicename = $servicename;
    }

    #endregion

    #region property methodname

    /** @var  string */
    protected $methodname;

    /**
     * @return string
     */
    public function getMethodname()
    {
        return $this->methodname;
    }

    /**
     * @param string $methodname
     */
    public function setMethodname($methodname)
    {
        $this->methodname = $methodname;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->methodname;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->setMethodname($method);
    }

    #endregion


    #region property mirror

    /** @var  array */
    protected $mirror = [];

    /**
     * @return array
     */
    public function getMirror()
    {
        return $this->mirror;
    }

    /**
     * @param array $mirror
     */
    public function setMirror($mirror)
    {
        $this->mirror = $mirror;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return isset($this->mirror['id']) ? $this->mirror['id'] : null;
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->mirror['id'] = $id;
    }

    #endregion
}

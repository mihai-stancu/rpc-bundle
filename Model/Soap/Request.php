<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\Soap;

use MS\RpcBundle\Model\RpcX\Request as RpcXRequest;

class Request extends RpcXRequest
{
    protected $version = 'http://www.w3.org/2003/05/soap-envelope';

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

    /**
     * @return array
     */
    public function getHeader()
    {
        return $this->getControl();
    }

    /**
     * @param array $header
     */
    public function setHeader($header)
    {
        $this->setControl($header);
    }

    /**
     * @return array|object
     */
    public function getBody()
    {
        return [
            $this->getMethod() => [
                $this->getParams(),
            ],
        ];
    }

    /**
     * @param array|object $body
     */
    public function setBody($body)
    {
        $this->setParams($body);
    }
}

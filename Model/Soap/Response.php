<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\Soap;

use MS\RpcBundle\Model\Rpc\Error;
use MS\RpcBundle\Model\RpcX\Response as RpcXResponse;

class Response extends RpcXResponse
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
    public function getBody()
    {
        if ($this->getError() !== null) {
            return $this->getError();
        }

        return $this->getResult();
    }

    /**
     * @param array $body
     */
    public function setBody($body)
    {
        if ($body instanceof Error) {
            $this->setError($body);

            return;
        }

        $this->setResult($body);
    }
}

<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\JsonWsp;

use MS\RpcBundle\Model\RpcX\Interfaces\Response as RpcXResponseInterface;
use MS\RpcBundle\Model\RpcX\Traits\Response as RpcXResponseTrait;

class Response implements RpcXResponseInterface
{
    use Traits\Message;
    use RpcXResponseTrait;

    const VERSION = '1.0';

    /**
     * @return Fault
     */
    public function getFault()
    {
        return $this->getError();
    }

    /**
     * @param Fault $fault
     */
    public function setFault(Fault $fault = null)
    {
        $this->setError($fault);
    }
}

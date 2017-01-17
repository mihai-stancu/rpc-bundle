<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Tests\Model;

use MS\RpcBundle\Model\Rpc\Error;
use MS\RpcBundle\Model\Rpc\Interfaces\Response as RpcResponseInterface;
use MS\RpcBundle\Model\Rpc\Response as RpcResponse;

class RpcResponseTest extends ModelTest
{
    /**
     * @return RpcResponse
     */
    public function dataProviderObjects()
    {
        return array(
            new RpcResponse(),
        );
    }

    /**
     * @return Error
     */
    public function dataProviderError()
    {
        return new Error();
    }

    /**
     * @return array|string[]
     */
    public function dataProviderInterfaces()
    {
        $tests = array();
        foreach ($this->dataProviderObjects() as $object) {
            $tests[] = array($object, RpcResponseInterface::class);
        }

        return $tests;
    }

    /**
     * @return array
     */
    public function dataProviderAccessors()
    {
        $tests = array();
        foreach ($this->dataProviderObjects() as $object) {
            $tests[] = array(
                $object,
                'version',
                '1.0',
            );
            $tests[] = array(
                $object,
                'result',
                array(1, 2, 3, 4),
            );
            $tests[] = array(
                $object,
                'error',
                $this->dataProviderError(),
            );
            $tests[] = array(
                $object,
                'id',
                123456789,
            );
        }

        return $tests;
    }
}

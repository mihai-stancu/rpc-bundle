<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Tests\Model;

use MS\RpcBundle\Model\Rpc\Interfaces\Request as RpcRequestInterface;
use MS\RpcBundle\Model\Rpc\Request as RpcRequest;

class RpcRequestTest extends ModelTest
{
    /**
     * @return RpcRequest
     */
    public function dataProviderObjects()
    {
        return array(
            new RpcRequest(),
        );
    }

    /**
     * @return array|string[]
     */
    public function dataProviderInterfaces()
    {
        $tests = array();
        foreach ($this->dataProviderObjects() as $object) {
            $tests[] = array($object, RpcRequestInterface::class);
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
                'method',
                'method_name',
            );
            $tests[] = array(
                $object,
                'params',
                array(1, 2, 3, 4),
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

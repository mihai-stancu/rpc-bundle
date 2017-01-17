<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Tests\Model;

use MS\RpcBundle\Model\RpcX\Auth;
use MS\RpcBundle\Model\RpcX\Interfaces\Request as RpcXRequestInterface;
use MS\RpcBundle\Model\RpcX\Request as RpcXRequest;

class RpcXRequestTest extends RpcRequestTest
{
    /**
     * @return RpcXRequest
     */
    public function dataProviderObjects()
    {
        return array(
            new RpcXRequest(),
        );
    }

    /**
     * @return array|string[]
     */
    public function dataProviderInterfaces()
    {
        $tests = $this->dataProviderInterfaces();
        foreach ($this->dataProviderObjects() as $object) {
            $tests[] = array($object, RpcXRequestInterface::class);
        }

        return $tests;
    }

    /**
     * @return array
     */
    public function dataProviderAccessors()
    {
        $tests = parent::dataProviderAccessors();
        foreach ($this->dataProviderObjects() as $object) {
            $tests[] = array(
                $object,
                'control',
                array('short' => false, 'gzip' => true),
            );
            $tests[] = array(
                $object,
                'service',
                'service_name',
            );
            $tests[] = array(
                $object,
                'auth',
                array('id' => 'aaaaaaaa', 'token' => 'bbbbbbbbb'),
            );
        }

        return $tests;
    }
}

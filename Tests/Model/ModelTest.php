<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Tests\Model;

abstract class ModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array|object[]
     */
    abstract public function dataProviderObjects();

    /**
     * @return array|string[]
     */
    abstract public function dataProviderInterfaces();

    /**
     * @return array
     */
    abstract public function dataProviderAccessors();

    /**
     * @param object $object
     * @param string $interface
     */
    public function testInterface($object, $interface)
    {
        $this->assertInstanceOf(get_class($object), $interface);
    }

    /**
     * @group Model
     * @dataProvider dataProviderAccessors
     *
     * @param object $object
     * @param string $accessor
     * @param mixed  $expected
     */
    public function testAccessor($object, $accessor, $expected)
    {
        $object->{'set'.$accessor}($expected);
        $actual = $object->{'get'.$accessor}();

        $this->assertEquals($expected, $actual);
    }
}

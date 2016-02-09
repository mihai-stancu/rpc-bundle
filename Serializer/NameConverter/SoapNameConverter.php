<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Serializer\NameConverter;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class SoapNameConverter implements NameConverterInterface
{
    /**
     * @param string $name
     *
     * @return string
     */
    public function denormalize($name)
    {
        $start = strpos($name, ':');

        return substr($name, $start);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function normalize($name)
    {
        switch ($name) {
            case 'soap':
                return '@xlmns:soap';

            case 'envelope':
            case 'header':
            case 'body':
            case 'result':
            case 'fault':
            case 'faultMessage':
            case 'faultCode':
                return 'soap:'.ucfirst($name);

            default:
                return 'm:'.ucfirst($name);
        }
    }
}

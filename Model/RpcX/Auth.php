<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\RpcX;

use MS\RpcBundle\Model\RpcX\Interfaces\Auth as RpcXAuthInterface;

class Auth implements RpcXAuthInterface
{
    use Traits\Auth;
}

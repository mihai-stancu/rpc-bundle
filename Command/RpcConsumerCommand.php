<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class RpcConsumerCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this->setName('ms:rpc:consumer');
    }
}

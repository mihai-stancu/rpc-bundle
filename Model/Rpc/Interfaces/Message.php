<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\Rpc\Interfaces;

interface Message
{
    /**
     * @return string
     */
    public function getVersion();

    /**
     * @param string $version
     */
    public function setVersion($version);

    /**
     * @return int|string
     */
    public function getId();

    /**
     * @param int|string $id
     */
    public function setId($id);
}

<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\Rpc\Traits;

trait Message
{
    #region property version

    protected $version;

    /**
     * @return string
     */
    public function getVersion()
    {
        if ($this->version === null and defined(static::class.'::VERSION')) {
            $this->version = constant(static::class.'::VERSION');
        }

        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    #endregion

    #region property id

    /**
     * @var int|string
     */
    protected $id;

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    #endregion
}

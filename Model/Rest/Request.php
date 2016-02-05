<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\Rest;

use MS\RpcBundle\Model\Rpc\Request as RpcRequest;

class Request extends RpcRequest
{
    /**
     * @return string
     */
    public function getAction()
    {
        return $this->getMethod();
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->setMethod($action);
    }

    /**
     * @return int|string
     */
    public function getResource()
    {
        $params = $this->getParams();

        if (isset($params['resource'])) {
            return $params['resource'];
        }
    }

    /**
     * @param int|string $resource
     */
    public function setResource($resource)
    {
        $params = (array) $this->getParams();
        $params['resource'] = $resource;

        $this->setParams($params);
    }
}

<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\Soap;

use MS\RpcBundle\Model\Rpc\Traits\Request as RpcRequestTrait;
use MS\RpcBundle\Model\Rpc\Traits\Response as RpcResponseTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class Body implements NormalizableInterface, DenormalizableInterface
{
    use RpcRequestTrait;
    use RpcResponseTrait;

    #region property url

    /** @var  string */
    protected $url = 'http://soap.com/method';

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    #endregion

    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = array())
    {
        var_dump($data);
    }

    public function normalize(NormalizerInterface $normalizer, $format = null, array $context = array())
    {
        if ($this->getError()) {
            return $normalizer->normalize($this->getError(), $format, $context);
        }

        if ($this->getMethod() and $this->getParams()) {
            $params = [];
            foreach ($this->getParams() as $i => $param) {
                $params['m:Item'][] = $param;
            }

            return [
                '@xmlns:m' => $this->getUrl(),
                'm:'.ucfirst($this->getMethod()) => $params,
            ];
        }

        if ($this->getResult()) {
            return [
                $this->getMethod().'Response' => $this->getParams(),
            ];
        }
    }
}

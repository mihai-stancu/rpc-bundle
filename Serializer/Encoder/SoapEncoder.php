<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Serializer\Encoder;

use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Encoder\NormalizationAwareInterface;
use Symfony\Component\Serializer\Encoder\SerializerAwareEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class SoapEncoder extends SerializerAwareEncoder implements EncoderInterface, DecoderInterface, NormalizationAwareInterface
{
    const FORMAT = 'soap';

    /** @var  Serializer */
    protected $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        if (!($serializer instanceof Serializer)) {
            throw new \InvalidArgumentException('The serializer does not have a normalizer/denormalizer attached.');
        }

        parent::setSerializer($serializer);
    }

    /**
     * @param string $string
     * @param string $format
     * @param array  $context
     *
     * @return array
     */
    public function decode($string, $format = null, array $context = [])
    {
        $array = $this->serializer->decode($string, $context['encoding'], $context);

        return $array;
    }

    /**
     * @param string $format
     *
     * @return bool
     */
    public function supportsDecoding($format)
    {
        return static::FORMAT === $format;
    }

    /**
     * @param object $object
     * @param string $format
     * @param array  $context
     *
     * @return array
     */
    public function encode($object, $format, array $context = [])
    {
        $context['xml_root_node_name'] = 'envelope';

        $array = $this->serializer->normalize($object, $format);

        return $this->serializer->encode($array, $context['encoding'], $context);
    }

    /**
     * @param string $format
     *
     * @return bool
     */
    public function supportsEncoding($format)
    {
        return static::FORMAT === $format;
    }
}

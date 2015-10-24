<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class DateTimeNormalizer  extends SerializerAwareNormalizer implements NormalizerInterface, DenormalizerInterface
{
    protected static $formats = [
        'integer',
        'string',
        'array',
        'object',
    ];

    /**
     * @param string $data
     * @param string $class
     * @param string $format
     * @param array  $context
     *
     * @return \DateTime
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return \DateTime::createFromFormat(\DateTime::ISO8601, $data);
    }

    /**
     * @param string $data
     * @param string $type
     * @param string $format
     *
     * @return bool
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return is_string($data) and \DateTime::createFromFormat(\DateTime::ISO8601, $data);
    }

    /**
     * @param \DateTime $object
     * @param string    $format
     * @param array     $context
     *
     * @return string
     */
    public function normalize($object, $format = null, array $context = [])
    {
        switch ($format) {
            case 'integer':
                return $object->getTimestamp();

            default:
            case 'string':
                return $object->format(\DateTime::ISO8601);

            case 'array':
                return getdate($object->getTimestamp());

            case 'object':
                return (object) getdate($object->getTimestamp());
        }
    }

    /**
     * @param \DateTime $data
     * @param string    $format
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return is_object($data) and is_a($data, \DateTime::class);
    }
}

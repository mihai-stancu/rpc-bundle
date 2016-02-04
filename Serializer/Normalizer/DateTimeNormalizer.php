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
    protected static $denormalizationFormats = [
        'Y-m-d H:i:s',
        'Y-m-d H:i:s.u',
        \DateTime::ISO8601,
        \DateTime::ATOM,
        \DateTime::COOKIE,
        \DateTime::ISO8601,
        \DateTime::RFC822,
        \DateTime::RFC850,
        \DateTime::RFC1036,
        \DateTime::RFC1123,
        \DateTime::RFC2822,
        \DateTime::RFC3339,
        \DateTime::RSS,
        \DateTime::W3C,
    ];

    protected static $normalizationFormats = [
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
        if (is_int($data)) {
            $result = new \DateTime();
            $result->setTimestamp($data);
        }

        foreach (static::$denormalizationFormats as $format) {
            if ($result = \DateTime::createFromFormat($format, $data)) {
                return $result;
            }
        }
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
        if (!is_int($data) and !is_string($data)) {
            return false;
        }

        if ($type !== null and $type !== \DateTime::class) {
            return false;
        }

        if (!is_int($data) and strtotime($data) === false) {
            return false;
        }

        return true;
    }

    /**
     * @param \DateTime $object
     * @param string    $format
     * @param array     $context
     *
     * @return string
     */
    public function normalize($object, $format = 'string', array $context = [])
    {
        switch ($format) {
            case 'integer':
                return $object->getTimestamp();

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
        if ($format === null or !in_array($format, static::$normalizationFormats)) {
            return false;
        }

        return $data InstanceOf \DateTime;
    }
}

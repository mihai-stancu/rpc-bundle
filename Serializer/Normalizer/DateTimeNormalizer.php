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
    protected static $stringDenormalizationFormats = [
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

            return $result->setTimestamp($data);
        }

        if (is_string($data)) {
            $formats = static::$stringDenormalizationFormats;

            if (isset($context['datetime_format'])) {
                $formats = array($context['datetime_format']);
            }

            foreach ($formats as $format) {
                if ($result = \DateTime::createFromFormat($format, $data)) {
                    return $result;
                }
            }
        }

        if (is_array($data) or (is_object($data) and $data instanceof \stdClass)) {
            $data = (array) $data;
            $timestamp = mktime(
                isset($data['hours']) ? $data['hours'] : date('H'),
                isset($data['minutes']) ? $data['minutes'] : date('i'),
                isset($data['seconds']) ? $data['seconds'] : date('s'),

                isset($data['mon']) ? $data['mon'] : date('n'),
                isset($data['mday']) ? $data['mday'] : date('j'),
                isset($data['year']) ? $data['year'] : date('Y')
            );

            $result = new \DateTime();

            return $result->setTimestamp($timestamp);
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
        if (!is_int($data) and !is_string($data) and !is_array($data) and !is_object($data)) {
            return false;
        }

        if ($type !== null and in_array($type, array(\DateTime::class, \DateTimeImmutable::class))) {
            return false;
        }

        if (is_string($data) and strtotime($data) === false) {
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
    public function normalize($object, $format = 'string', array $context = array('type' => 'string'))
    {
        $type = isset($context['string']) ? $context['string'] : null;
        switch ($type) {
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
        return in_array($format, static::$normalizationFormats)
           and $data instanceof \DateTime;
    }
}

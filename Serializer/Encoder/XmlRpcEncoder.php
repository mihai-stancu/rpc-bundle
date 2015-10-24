<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Serializer\Encoder;

use MS\RpcBundle\Model\RpcRequest;
use MS\RpcBundle\Model\RpcResponse;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Encoder\NormalizationAwareInterface;
use Symfony\Component\Serializer\Encoder\SerializerAwareEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class XmlRpcEncoder extends SerializerAwareEncoder implements EncoderInterface, DecoderInterface, NormalizationAwareInterface
{
    const FORMAT = 'xml-rpc';

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
        $array = $this->decodeValue($array);

        return $array;
    }

    /**
     * @param mixed $value
     * @param int   $level
     *
     * @return mixed
     */
    public function decodeValue($value, $level = 0)
    {
        $result = null;

        if (is_array($value)) {
            /*
             * Scalars
             */
            if ($level === 0) {
                $result = [];
                foreach ($value as $name => $subvalue) {
                    if ($name === 'params' and !empty($subvalue)) {
                        foreach ($subvalue['param'] as $i => $item) {
                            if (is_int($i)) {
                                $result['params'][] = $this->decodeValue($item['value'], $level + 2);
                            } else {
                                $result['params'][$i] = $this->decodeValue($item['value'], $level + 2);
                            }
                        }
                    } elseif ($name === 'fault' and empty($subvalue)) {
                        $result['fault'] = null;
                    } else {
                        $result[$name] = $subvalue;
                    }
                }
            } elseif (array_key_exists('nil', $value)) {
                $result = null;
            } elseif (array_key_exists('boolean', $value)) {
                $result = (boolean) $value['boolean'];
            } elseif (array_key_exists('int', $value)) {
                $result = (integer) $value['int'];
            } elseif (array_key_exists('i4', $value)) {
                $result = (integer) $value['i4'];
            } elseif (array_key_exists('double', $value)) {
                $result = (double) $value['double'];
            } elseif (array_key_exists('dateTime.iso8601', $value)) {
                $result = new \DateTime($value['dateTime.iso8601']);
            } elseif (array_key_exists('string', $value)) {
                $result = (string) $value['string'];
            } elseif (array_key_exists('base64', $value)) {
                $result = base64_decode($value['base64']);
            } elseif (array_key_exists('value', $value)) {
                $result = $this->decodeValue($value['value'], $level + 1);
            } elseif (array_key_exists('array', $value)) {
                $result = [];
                foreach ($value['array']['data'] as $i => $item) {
                    $result[$i] = $this->decodeValue($item['value'], $level + 1);
                }
            } elseif (array_key_exists('struct', $value)) {
                $result = [];
                foreach ($value['struct']['member'] as $item) {
                    $name = $item['name'];
                    $result[$name] = $this->decodeValue($item['value'], $level + 1);
                }
            }
        } else {
            $result = $value;
        }

        return $result;
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
        if ($object instanceof RpcRequest or $object instanceof RpcRequest) {
            $context['xml_root_node_name'] = 'methodCall';
        } elseif ($object instanceof RpcResponse) {
            $context['xml_root_node_name'] = 'methodResponse';
        }

        $array = $this->serializer->normalize($object, $format);
        $array = $this->encodeValue($array);

        return $this->serializer->encode($array, $context['encoding'], $context);
    }

    /**
     * @param mixed $value
     * @param int   $level
     *
     * @return mixed
     */
    protected function encodeValue($value, $level = 0)
    {
        $result = null;

        if ($level === 0) {
            foreach ($value as $name => $subvalue) {
                if ($name === 'params' and !empty($subvalue)) {
                    foreach ($subvalue as $i => $item) {
                        $result['params']['param'][$i]['value'] = $this->encodeValue($item, $level + 2);
                    }
                } else {
                    $result[$name] = $subvalue;
                }
            }
        } elseif (is_null($value)) {
            $result['nil'] = '';
        } elseif (is_bool($value)) {
            $result['boolean'] = $value;
        } elseif (is_int($value)) {
            $result['i4'] = $value;
        } elseif (is_float($value)) {
            $result['double'] = $value;
        } elseif ((is_object($value) and $value instanceof \DateTime)
        or (is_string($value) and \DateTime::createFromFormat(\DateTIme::ISO8601, $value))) {
            $value = \DateTime::createFromFormat(\DateTIme::ISO8601, $value);
            $result['dateTime.iso8601'] = $value->format(\DateTime::ISO8601);
        } elseif (is_string($value) and mb_detect_encoding($value, ['ASCII', 'UTF-8'], true)) {
            $result['string'] = $value;
        } elseif (is_string($value)) {
            $result['base64'] = base64_encode($value);
        } elseif (is_array($value) and is_int(key($value))) {
            foreach ($value as $i => $item) {
                $result['array']['data'][]['value'] = $this->encodeValue($item, $level + 1);
            }
        } elseif (is_object($value) or (is_array($value) and is_string(key($value)))) {
            foreach ($value as $name => $item) {
                $result['struct']['member'][] = [
                    'name' => $name,
                    'value' => $this->encodeValue($item, $level + 1),
                ];
            }
        }

        return $result;
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

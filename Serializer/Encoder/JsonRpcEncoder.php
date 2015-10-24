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
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

class JsonRpcEncoder extends SerializerAwareEncoder implements EncoderInterface, DecoderInterface, NormalizationAwareInterface, SerializerAwareInterface
{
    const REGEX_DATA_URI = '/^data:(?<mime>[\w-]++\/[\w-]++);(b64|base64),(?<value>[a-zA-Z0-9+\/=]++)/';

    protected static $formats = [
        'json-rpc',
        'json-rpc-x',
        'json-rpc-xs',
    ];

    protected static $encodingAllowsBinaryStrings = [
        'cbor',
        'igbinary',
        'msgpack',
        'serialize',
        'tnetstring',
        'ubjson',
    ];

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
     * @param null   $format
     * @param array  $context
     *
     * @return mixed
     */
    public function decode($string, $format = null, array $context = [])
    {
        $array = $this->serializer->decode($string, $context['encoding'], $context);
        $array = $this->decodeValue($array);

        return $array;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function decodeValue($value)
    {
        $result = null;

        if (is_string($value) and $result = \DateTime::createFromFormat(\DateTime::ISO8601, $value)) {
        } elseif (is_string($value) and preg_match(static::REGEX_DATA_URI, $value, $data)) {
            $result = $this->decodeBinaryValue($data['mime'], $data['value']);
        } elseif (is_array($value) or is_object($value)) {
            $result = [];
            foreach ($value as $name => $item) {
                if (is_string($name)) {
                    $result[$name] = $this->decodeValue($item);
                } else {
                    $result[] = $this->decodeValue($item);
                }
            }
        } else {
            $result = $value;
        }

        return $result;
    }

    protected function decodeBinaryValue($mime, $value)
    {
        $value = base64_decode($value);

        switch ($mime) {
            case 'app/c-gz':
            case 'application/x-gzip':
                $value = gzdecode($value);
                break;

            case 'app/c-bz':
            case 'application/x-bzip2':
                $value = bzdecompress($value);
                break;

            case 'app/c-lzf':
            case 'application/x-lzf':
                $value = lzf_decompress($value);
                break;
        }

        return $value;
    }

    /**
     * @param string $format
     *
     * @return bool
     */
    public function supportsDecoding($format)
    {
        return in_array($format, static::$formats, true);
    }

    /**
     * @param object $object
     * @param string $format
     * @param array  $context
     *
     * @return array
     */
    public function encode($object, $format = null, array $context = [])
    {
        $array = $this->serializer->normalize($object, $format);
        $allowBinaryString = in_array($context['encoding'], static::$encodingAllowsBinaryStrings, true);
        $array = $this->encodeValue($array, $allowBinaryString);

        return $this->serializer->encode($array, $context['encoding'], $context);
    }

    /**
     * @param mixed $value
     * @param bool  $forBinary
     *
     * @return mixed
     */
    protected function encodeValue($value, $forBinary = false)
    {
        $result = null;

        if (is_null($value)) {
            $result = null;
        } elseif (is_bool($value)) {
            $result = $value;
        } elseif (is_object($value) and $value instanceof \DateTime) {
            $result = $value->format(\DateTime::ISO8601);
        } elseif (is_string($value) and ($forBinary or mb_detect_encoding($value, ['ASCII', 'UTF-8'], true))) {
            $result = $value;
        } elseif (is_string($value) and !$forBinary) {
            $result = $this->encodeBinaryValue('application/octet-stream', $value);
        } elseif (is_array($value) or is_object($value)) {
            $result = [];
            foreach ($value as $name => $item) {
                if (is_string($name)) {
                    $result[$name] = $this->encodeValue($item, $forBinary);
                } else {
                    $result[] = $this->encodeValue($item, $forBinary);
                }
            }
        } else {
            $result = $value;
        }

        return $result;
    }

    protected function encodeBinaryValue($mime, $data)
    {
        if (strlen($data) > 512) {
            $mime = 'application/x-gzip';
            $data = gzencode($data);
        }

        $result = 'data:'.$mime.';base64,'.base64_encode($data);

        return $result;
    }

    /**
     * @param string $format
     *
     * @return bool
     */
    public function supportsEncoding($format)
    {
        return in_array($format, static::$formats, true);
    }
}

<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Tests;

use MS\RpcBundle\Factory\RequestFactory;
use MS\RpcBundle\Factory\ResponseFactory;
use MS\RpcBundle\Model\Rpc\Error;
use MS\RpcBundle\Model\Rpc\Request;
use MS\RpcBundle\Model\Rpc\Response;
use MS\RpcBundle\Model\Soap\Fault as SoapFault;
use MS\RpcBundle\Model\XmlRpc\Fault as XmlRpcFault;
use MS\RpcBundle\Serializer\Encoder\RpcEncoder;
use MS\RpcBundle\Serializer\Encoder\SoapEncoder;
use MS\RpcBundle\Serializer\Encoder\XmlRpcEncoder;
use MS\RpcBundle\Serializer\Normalizer\JSendNormalizer;
use MS\RpcBundle\Serializer\Normalizer\JsonRpcXSNormalizer;
use MS\RpcBundle\Serializer\Normalizer\RestNormalizer;
use MS\RpcBundle\Serializer\Normalizer\RpcNormalizer;
use MS\RpcBundle\Serializer\Normalizer\SoapNormalizer;
use MS\RpcBundle\Serializer\Normalizer\XmlRpcNormalizer;
use MS\SerializerBundle\Serializer\Normalizer\ArrayDenormalizer;
use MS\SerializerBundle\Serializer\Normalizer\DataUriNormalizer;
use MS\SerializerBundle\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;
use Symfony\Component\Serializer\Serializer;

class RpcSerializerTest extends \PHPUnit_Framework_TestCase
{
    protected static $protocols = [
        //'jsend',
        'json-rpc',
        'json-rpc-x',
        'json-rpc-xs',
        'rest',
        'rpc',
        'rpc-x',
        //'soap',
        'xml-rpc',
    ];

    protected static $encoders = [
        //'bencode' => 'MS\SerializerBundle\Serializer\Encoder\BencodeEncoder',
        'bson' => 'MS\SerializerBundle\Serializer\Encoder\BsonEncoder',
        'cbor' => 'MS\SerializerBundle\Serializer\Encoder\CborEncoder',
        'export' => 'MS\SerializerBundle\Serializer\Encoder\ExportEncoder',
        //'form' => 'MS\SerializerBundle\Serializer\Encoder\FormEncoder',
        'igbinary' => 'MS\SerializerBundle\Serializer\Encoder\IgbinaryEncoder',
        'json' => 'Symfony\Component\Serializer\Encoder\JsonEncoder',
        'msgpack' => 'MS\SerializerBundle\Serializer\Encoder\MsgpackEncoder',
        'rison' => 'MS\SerializerBundle\Serializer\Encoder\RisonEncoder',
        'serialize' => 'MS\SerializerBundle\Serializer\Encoder\SerializeEncoder',
        'tnetstring' => 'MS\SerializerBundle\Serializer\Encoder\TnetstringEncoder',
        'ubjson' => 'MS\SerializerBundle\Serializer\Encoder\UbjsonEncoder',
        'yaml' => 'MS\SerializerBundle\Serializer\Encoder\YamlEncoder',
        'xml' => 'Symfony\Component\Serializer\Encoder\XmlEncoder',
    ];

    protected static $cases = [
        'notification',
        'request',
        'responseError',
        'responseSuccess',
    ];

    /** @var  Serializer */
    protected $serializer;

    /** @var  RequestFactory */
    protected $requestFactory;

    /** @var  ResponseFactory */
    protected $responseFactory;

    public function setUp()
    {
        $propertyInfo = new PropertyInfoExtractor([], [new ReflectionExtractor()]);

        /** @var SerializerAwareNormalizer[] $normalizers */
        $normalizers = [];
        $normalizers[] = new ArrayDenormalizer();
        $normalizers[] = new DateTimeNormalizer();
        $normalizers[] = new DataUriNormalizer();
        $normalizers[] = new JSendNormalizer(null, null, $propertyInfo);
        $normalizers[] = new JsonRpcXSNormalizer(null, null, $propertyInfo);
        $normalizers[] = new RestNormalizer(null, null, $propertyInfo);
        $normalizers[] = new RpcNormalizer(null, null, $propertyInfo);
        $normalizers[] = new SoapNormalizer(null, null, $propertyInfo);
        $normalizers[] = new XmlRpcNormalizer(null, null, $propertyInfo);
        $normalizers[] = new GetSetMethodNormalizer();

        $encoders = [];
        $encoders[] = new RpcEncoder();
        $encoders[] = new SoapEncoder();
        $encoders[] = new XmlRpcEncoder();

        foreach (static::$encoders as $encoding => $class) {
            if (!method_exists($class, 'isInstalled') or $class::isInstalled()) {
                $encoders[] = new $class();
            }
        }

        $this->serializer = new Serializer($normalizers, $encoders);
        $this->requestFactory = new RequestFactory($this->serializer);
        $this->responseFactory = new ResponseFactory($this->serializer);

        foreach ($normalizers as $normalizer) {
            if (method_exists($normalizer, 'setSerializer')) {
                $normalizer->setSerializer($this->serializer);
            }
        }
    }

    protected function buildData()
    {
        return [
            null,
            true,
            false,
            1,
            1.23,
            'The quick brown fox jumps over the lazy dog.',
            [1, 2, 3, 4, 5],
            ['a' => 'a', 'b' => 'b', 'c' => 'c'],
            //\DateTime::createFromFormat(\DateTime::ISO8601, '2014-04-03T12:00:33+0000'),
            //new \SplFileObject(__DIR__.'/data/beacon.gif'),
        ];
    }

    protected function buildNotification($protocol)
    {
        /** @var Request $object */
        $object = $this->requestFactory->create($protocol);

        $object->setMethod('core.default.index');
        $object->setParams(
            $this->buildData()
        );

        return $object;
    }

    protected function buildRequest($protocol)
    {
        $object = $this->buildNotification($protocol);
        $object->setId(123456789);

        return $object;
    }

    protected function buildResponseSuccess($protocol)
    {
        /** @var Response $object */
        $object = $this->responseFactory->create($protocol);

        $object->setResult($this->buildData());

        return $object;
    }

    protected function buildResponseError($protocol)
    {
        /** @var Response $object */
        $object = $this->responseFactory->create($protocol);

        $error = new Error();
        if ($protocol === 'xml-rpc') {
            $error = new XmlRpcFault();
        }
        if ($protocol === 'soap') {
            $error = new SoapFault();
        }

        $object->setError($error);
        $object->getError()->setCode(33);
        $object->getError()->setMessage('Freak Occurence');

        return $object;
    }

    public function dataProviderRpcSerializer()
    {
        global $argv;
        if (isset($argv[2])) {
            static::$protocols = [$argv[2]];
        }
        if (isset($argv[4])) {
            static::$encoders = [
                $argv[4] => static::$encoders[$argv[4]],
            ];
        }

        $tests = [];
        foreach (static::$protocols as $protocol) {
            foreach (static::$encoders as $encoding => $class) {
                foreach (static::$cases as $case) {
                    if ((in_array($protocol, ['xml-rpc', 'soap']) and $encoding !== 'xml')
                    or  ($encoding === 'xml' and !in_array($protocol, ['xml-rpc', 'soap']))) {
                        continue;
                    }

                    $tests[] = [
                        'protocol' => $protocol,
                        'encoding' => $encoding,
                        'case' => $case,
                    ];
                }
            }
        }

        return $tests;
    }

    /**
     * @dataProvider dataProviderRpcSerializer
     *
     * @param string $protocol
     * @param string $encoding
     * @param string $case
     */
    public function testRpcSerializer($protocol, $encoding, $case)
    {
        try {
            /** @var Serializer $serializer */
            $serializer = $this->serializer;

            $expected = call_user_func([$this, 'build'.ucfirst($case)], $protocol);
            $string = $serializer->serialize($expected, $protocol, ['encoding' => $encoding]);
            $actual = $serializer->deserialize($string, get_class($expected), $protocol, ['encoding' => $encoding]);

            $this->assertEquals(
                $expected,
                $actual,
                $encoding.' failed to encode/decode an object identically.',
                //."\n".$string."\n",
                0.0001
            );
        } catch (RuntimeException $ex) {
            $this->markTestSkipped('Test skipped due to missing dependency: '.$ex->getMessage());
        }
    }
}

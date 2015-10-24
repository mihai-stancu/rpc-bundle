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
use MS\RpcBundle\Model\RpcError;
use MS\RpcBundle\Model\RpcRequest;
use MS\RpcBundle\Model\RpcResponse;
use MS\RpcBundle\Serializer\Normalizer\JsonRpcXSNormalizer;
use MS\RpcBundle\Serializer\Normalizer\RpcNormalizer;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;
use Symfony\Component\Serializer\Serializer;

class RpcSerializerTest extends \PHPUnit_Framework_TestCase
{
    protected static $protocols = [
        'json-rpc',
        'json-rpc-x',
        'json-rpc-xs',
        'xml-rpc',
    ];

    protected static $encoders = array(
        //'bencode' => 'MS\SerializerBundle\Serializer\Encoder\BencodeEncoder',
        'bson' => 'MS\SerializerBundle\Serializer\Encoder\BsonEncoder',
        'cbor' => 'MS\SerializerBundle\Serializer\Encoder\CborEncoder',
        'export' => 'MS\SerializerBundle\Serializer\Encoder\ExportEncoder',
        'igbinary' => 'MS\SerializerBundle\Serializer\Encoder\IgbinaryEncoder',
        'msgpack' => 'MS\SerializerBundle\Serializer\Encoder\MsgpackEncoder',
        'rison' => 'MS\SerializerBundle\Serializer\Encoder\RisonEncoder',
        //'sereal' => 'MS\SerializerBundle\Serializer\Encoder\SerealEncoder',
        'serialize' => 'MS\SerializerBundle\Serializer\Encoder\SerializeEncoder',
        //'smile' => 'MS\SerializerBundle\Serializer\Encoder\SmileEncoder',
        'tnetstring' => 'MS\SerializerBundle\Serializer\Encoder\TnetstringEncoder',
        'ubjson' => 'MS\SerializerBundle\Serializer\Encoder\UbjsonEncoder',
        'yaml' => 'MS\SerializerBundle\Serializer\Encoder\YamlEncoder',
    );

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
        /** @var SerializerAwareNormalizer[] $normalizers */
        $normalizers = array();
        $normalizers[] = new JsonRpcXSNormalizer();
        $normalizers[] = new RpcNormalizer();
        $normalizers[] = new GetSetMethodNormalizer();

        $encoders = array();
        foreach (static::$encoders as $encoding => $class) {
            if (method_exists($class, 'isInstalled') and $class::isInstalled()) {
                $encoders[] = new $class();
            }
        }

        $this->serializer = new Serializer($normalizers, $encoders);
        $this->requestFactory = new RequestFactory($this->serializer);
        $this->responseFactory = new ResponseFactory($this->serializer);

        foreach ($normalizers as $normalizer) {
            $normalizer->setSerializer($this->serializer);
        }
    }

    protected function buildData()
    {
        return [
            null,
            true,
            false,
            new \DateTime('2014-04-03T12:00:33+0000'),
            1,
            1.23,
            'The quick brown fox jumps over the lazy dog.',
            base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'),
            [1, 2, 3, 4, 5],
            ['a' => 'a', 'b' => 'b', 'c' => 'c'],
        ];
    }

    protected function buildNotification($protocol)
    {
        /** @var RpcRequest $object */
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
        /** @var RpcResponse $object */
        $object = $this->responseFactory->create($protocol);

        $object->setResult($this->buildData());

        return $object;
    }

    protected function buildResponseError($protocol)
    {
        /** @var RpcResponse $object */
        $object = $this->responseFactory->create($protocol);

        $object->setError(new RpcError());
        $object->getError()->setCode(33);
        $object->getError()->setMessage('Freak Occurence');

        return $object;
    }

    public function dataProviderRpcSerializer()
    {
        $tests = [];

        foreach (static::$protocols as $protocol) {
            foreach (static::$encoders as $encoding => $class) {
                foreach (static::$cases as $case) {
                    if (($protocol === 'xml-rpc' and $encoding !== 'xml')
                    or  ($encoding === 'xml' and $protocol !== 'xml-rpc')) {
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
                $encoding.'failed to encode/decode an object identically.'
                ."\n".$string."\n",
                0.0001
            );
        } catch (RuntimeException $ex) {
            $this->markTestSkipped('Test skipped due to missing dependency: '.$ex->getMessage());
        }
    }
}

<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Model\JsonWsp;

class Description
{
    use Traits\Message;

    /**
     * @param \ReflectionClass $class
     */
    public function load(\ReflectionClass $class)
    {
        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
        $this->methods = $this->loadMethods($methods);
    }

    /**
     * @param \ReflectionClass $class
     *
     * @return array
     */
    protected function loadType(\ReflectionClass $class)
    {
        $types = [];
        foreach ($class->getProperties() as $property) {
            $name = $property->getName();

            if ($property->isPublic()
            or $class->hasMethod('get'.ucfirst($name))
            and $class->hasMethod('set'.ucfirst($name))) {
                $method = $class->getMethod('set'.ucfirst($name));

                /** @var \ReflectionParameter[] $parameters */
                $parameters = $method->getParameters();
                $parameter = $parameters[0];

                if ($parameter->getClass()) {
                    $types[$name] = $parameter->getClass()->getName();

                    $this->addType($parameter->getClass());
                } else {
                    $types[$name] = (string) $parameter->getType();
                }
            }
        }

        return $types;
    }

    /**
     * @param \ReflectionMethod[] $items
     *
     * @return array
     */
    protected function loadMethods($items)
    {
        $methods = [];
        foreach ($items as $item) {
            $name = $item->getName();

            $methods[$name] = [
                'doc_lines' => [],
                'params' => $this->loadParams($item->getParameters()),
                'ret_info' => [
                    'doc_lines' => [],
                    'type' => (string) $item->getReturnType(),
                ],
            ];
        }

        return $methods;
    }

    /**
     * @param \ReflectionParameter[] $items
     *
     * @return array
     */
    protected function loadParams($items)
    {
        $params = [];
        foreach ($items as $item) {
            $paramName = $item->getName();
            $params[$paramName] = [
                'def_order' => $item->getPosition(),
                'doc_lines' => [],
                'type' => $item->getClass()
                    ? $item->getClass()->getName()
                    : (string) $item->getType(),
                'optional' => $item->isOptional(),
            ];
        }

        return $params;
    }

    /** @var  string */
    protected $url;

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

    /** @var  array */
    protected $types;

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @param array $types
     */
    public function setTypes($types)
    {
        $this->types = $types;
    }

    public function addType(\ReflectionClass $class)
    {
        $name = $class->getName();
        if (!isset($this->types[$name])) {
            $this->types[$name] = $this->loadType($class);
        }
    }

    /** @var  array */
    protected $methods;

    /**
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @param array $methods
     */
    public function setMethods($methods)
    {
        $this->methods = $methods;
    }
}

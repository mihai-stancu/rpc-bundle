<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\RpcBundle\Proxy;

/**
 * The ProxyGenerator takes an interface or a class as an input and generates a
 * Proxy class which implements all of the public abstract methods from the
 * supplied interface or class.
 */
class Generator
{
    /**
     * @var string
     */
    protected $directory;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var
     */
    protected $trait;

    /**
     * @var string
     */
    protected $classTemplate = '<?php

namespace {$namespace};

class {$name} {$keyword} {$base}
{
    {$trait}

    {$methods}
}

';

    /**
     * @var string
     */
    protected $methodTemplate = '
    public function {$name}({$parameters})
    {
        return $this->call(__FUNCTION__, func_get_args());
    }
';

    protected $parameterTemplate = '{$type} {$name} {$default}';

    /**
     * @param string $directory
     * @param string $namespace
     * @param string $baseTrait
     */
    public function __construct($directory, $namespace, $baseTrait)
    {
        $this->directory = $directory;
        $this->namespace = trim($namespace, '\\');
        $this->trait = $baseTrait;
    }

    /**
     * @param string $class
     *
     * @return string
     */
    public function generateProxyClass($class)
    {
        $definition = $this->getClassDefinition($class);

        if (empty($definition)) {
            return $class;
        }

        $file = $this->directory.str_replace('\\', '/', $class).'.php';

        $directory = dirname($file);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($file, $definition);

        return $this->getProxyClassName($class);
    }

    /**
     * @param string $class
     *
     * @return string
     */
    protected function getProxyClassName($class)
    {
        return $this->namespace.'\\'.$class;
    }

    /**
     * @param string $interfaceOrClass
     *
     * @return string|null
     */
    protected function getClassDefinition($interfaceOrClass)
    {
        $reflectionClass = new \ReflectionClass($interfaceOrClass);

        if ($reflectionClass->isFinal() or $reflectionClass->isTrait()) {
            return;
        }

        $namespace = $this->namespace.'\\'.$reflectionClass->getNamespaceName();
        $name = $reflectionClass->getShortName();
        $trait = '';
        if (!array_key_exists($this->trait, $reflectionClass->getTraits())) {
            $trait = 'use \\'.$this->trait.';';
        }
        $keyword = $reflectionClass->isInterface() ? 'implements' : 'extends';
        $base = '\\'.$reflectionClass->getName();
        $methods = [];
        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            $methods[] = $this->getMethodDefinition($reflectionMethod);
        }

        if (empty($methods)) {
            return;
        }

        $methods = array_filter($methods);
        $methods = implode("\n", $methods);

        $replace = [
            '{$namespace}' => $namespace,
            '{$name}' => $name,
            '{$trait}' => $trait,
            '{$keyword}' => $keyword,
            '{$base}' => $base,
            '{$methods}' => $methods,
        ];

        $template = $this->classTemplate;
        $definition = str_replace(array_keys($replace), array_values($replace), $template);

        return $definition;
    }

    /**
     * @param \ReflectionMethod $reflectionMethod
     *
     * @return string|null
     */
    protected function getMethodDefinition(\ReflectionMethod $reflectionMethod)
    {
        if (!$reflectionMethod->isPublic() or !$reflectionMethod->isAbstract()
        or $reflectionMethod->isFinal() or $reflectionMethod->isStatic()) {
            return;
        }

        $name = $reflectionMethod->getName();
        $parameters = [];
        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            $parameters[] = $this->getParameterDefinition($reflectionParameter);
        }
        $parameters = array_filter($parameters);
        $parameters = implode(', ', $parameters);

        $replace = [
            '{$name}' => $name,
            '{$parameters}' => $parameters,
        ];

        $template = $this->methodTemplate;
        $definition = str_replace(array_keys($replace), array_values($replace), $template);

        return $definition;
    }

    /**
     * @param \ReflectionParameter $r
     *
     * @return string
     */
    protected function getParameterDefinition(\ReflectionParameter $r)
    {
        $type = '';
        if ($r->isArray()) {
            $type = 'array';
        } elseif ($r->isCallable()) {
            $type = 'callable';
        } elseif ($r->getClass()) {
            $type = '\\'.$r->getClass()->getName();
        }

        $name = $r->getName();

        $default = '';
        if ($r->isDefaultValueAvailable() and $r->isDefaultValueConstant()) {
            $default = ' = \\'.$r->getDefaultValueConstantName();
        } elseif ($r->isDefaultValueAvailable()) {
            $default = ' = '.var_export($r->getDefaultValue(), true);
        }

        $definition = trim($type.' $'.$name.$default);

        return $definition;
    }
}

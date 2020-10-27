<?php declare(strict_types=1);

namespace rkwadiga\simpledi;

use \ReflectionClass;
use \ReflectionProperty;
use \ReflectionMethod;
use \ReflectionException as BaseException;
use rkwadiga\simpledi\exception\ReflectionException;

class Reflection
{
    private ReflectionClass $reflection;
    private PhpDocReader $phpDocReader;
    private  ?array $configuration;
    private ?array $requiredConstructorParams;
    private ?array $optionalConstructorParams;
    private ?array $setters;
    private ?array $publicProperties;

    public function __construct(string $class)
    {
        $this->reflection = $this->getReflection($class);
        $this->phpDocReader = new PhpDocReader();
        $this->configuration = null;
        $this->requiredConstructorParams = null;
        $this->optionalConstructorParams = null;
        $this->setters = null;
        $this->publicProperties = null;
    }

    public function getConfiguration() : array
    {
        if ($this->configuration !== null) {
            return $this->configuration;
        }
        $doc = $this->reflection->getDocComment();
        return $this->configuration = $doc !== false ? $this->phpDocReader->read($doc) : [];
    }

    public function getRequiredConstructorParams() : array
    {
        if ($this->requiredConstructorParams !== null) {
            return $this->requiredConstructorParams;
        }
        $this->requiredConstructorParams = [];
        if (!$this->reflection->hasMethod('__construct')) {
            return $this->requiredConstructorParams;
        }
        foreach ($this->reflection->getMethod('__construct')->getParameters() as $param) {
            if ($param->isOptional()) {
                continue;
            }
            $this->requiredConstructorParams[$param->name] = $param->allowsNull() ? '?' . $param->getType() : (string)$param->getType();
        }
        return $this->requiredConstructorParams;
    }

    public function getOptionalConstructorParams() : array
    {
        if ($this->optionalConstructorParams !== null) {
            return $this->optionalConstructorParams;
        }
        $this->optionalConstructorParams = [];
        if (!$this->reflection->hasMethod('__construct')) {
            return $this->optionalConstructorParams;
        }
        foreach ($this->reflection->getMethod('__construct')->getParameters() as $param) {
            if ($param->isOptional()) {
                $this->optionalConstructorParams[$param->name] = $param->allowsNull() ? '?' . $param->getType() : (string)$param->getType();
            }
        }
        return $this->optionalConstructorParams;
    }

    public function getSetters() : array
    {
        if ($this->setters !== null) {
            return $this->setters;
        }
        $this->setters = [];
        foreach ($this->reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            // Only methods with names like "setSomething"
            if (substr($method->name, 0, 3) !== 'set') {
                continue;
            }

            // Get method parameters
            $params = $method->getParameters();

            // Only methods with one parameter
            if (count($params) !== 1) {
                continue;
            }

            $this->setters[lcfirst(substr($method->name, 3))] = (string)$params[0]->getType();
        }
        return $this->setters;
    }

    public function getPublicProperties() : array
    {
        if ($this->publicProperties !== null) {
            return $this->publicProperties;
        }
        $this->publicProperties = [];
        foreach ($this->reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
            $this->publicProperties[$prop->name] = (string)$prop->getType();
        }
        return $this->publicProperties;
    }

    public function createInstance(array $constructorParams) : object
    {
        return $this->reflection->newInstanceArgs($constructorParams);
    }

    private function getReflection(string $forClass) : ReflectionClass
    {
        try {
            return new ReflectionClass($forClass);
        } catch (BaseException $e) {
            throw new ReflectionException(sprintf('Can not reflect class "%s": %s', $forClass, $e->getMessage()), ReflectionException::INVALID_CLASS, $e);
        }
    }
}
<?php declare(strict_types=1);

namespace rkwadriga\simpledi\tests;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use rkwadriga\simpledi\tests\mock\ConfigurationTrait;

abstract class AbstractTest extends TestCase
{
    use ConfigurationTrait;

    protected function getPrivateProperty(object $object, string $propertyName)
    {
        $property = (new ReflectionClass(get_class($object)))->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }

    protected function callPrivateMethod(object $object, string $methodName, array $params)
    {
        $method = (new ReflectionClass(get_class($object)))->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $params);
    }

    protected function checkInstance(object $instance, string $class, ?array $privateProperties = null, ?array $publicProperties = null)
    {
        if ($privateProperties === null) {
            $privateProperties = array_merge($this->getRequiredConstructorParams(), $this->getOptionalConstructorParams());
        }
        if ($publicProperties === null) {
            $publicProperties = $this->getPublicProperties();
        }

        $this->assertInstanceOf($class, $instance);
        foreach ($privateProperties as $name => $value) {
            $this->assertEquals($value, $this->getPrivateProperty($instance, $name));
        }
        foreach ($publicProperties as $name => $value) {
            $this->assertEquals($value, $instance->$name);
        }
    }

    protected function createObject(string $class, array $params = []) : object
    {
        $params = array_merge($this->params, $params);
        $reflection = new ReflectionClass($class);
        $instance = $reflection->newInstanceArgs($params);
        foreach ($params as $name => $value) {
            $setter = 'set' . ucfirst($name);
            if (method_exists($instance, $setter)) {
                call_user_func([$instance, $setter], $value);
            } elseif (property_exists($instance, $name) && (new ReflectionProperty($class, $name))->isPublic()) {
                $instance->$name = $value;
            }
        }
        return $instance;
    }
}
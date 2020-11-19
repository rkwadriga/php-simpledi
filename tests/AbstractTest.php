<?php declare(strict_types=1);

namespace rkwadriga\simpledi\tests;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
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
}
<?php declare(strict_types=1);

namespace rkwadriga\simpledi\tests;

use ReflectionClass;
use rkwadiga\simpledi\Container;
use rkwadiga\simpledi\Reflection;
use rkwadriga\simpledi\tests\mock\ConfigurationTrait;
use rkwadiga\simpledi\exception\ReflectionException;
use rkwadriga\simpledi\tests\mock\Scoped_3;

/**
 * Command ./run ReflectionTest
 */
class ReflectionTest extends AbstractTest
{
    use ConfigurationTrait;

    public function testGetReflection()
    {
        $reflection = new Reflection(Scoped_3::class);
        $classReflection1 = $this->getPrivateProperty($reflection, 'reflection');
        $this->assertInstanceOf(ReflectionClass::class, $classReflection1);
        $this->assertEquals(Scoped_3::class, $classReflection1->name);
        $this->assertEquals(Scoped_3::class, $reflection->getClass());

        $classReflection2 = $this->callPrivateMethod($reflection, 'getReflection', [Scoped_3::class]);
        $this->assertEquals($classReflection1, $classReflection2);
    }

    public function testGetConfiguration()
    {
        $reflection = new Reflection(Scoped_3::class);
        $checkConfiguration = $this->getParams(Container::SCOPED);
        $configuration = $reflection->getConfiguration();
        $this->assertEquals($checkConfiguration, $configuration);
    }

    public function testGetRequiredConstructorParams()
    {
        $reflection = new Reflection(Scoped_3::class);
        $params = $reflection->getRequiredConstructorParams();
        $this->assertEquals($this->requiredConstructorParams, $params);
    }

    public function testGetOptionalConstructorParams()
    {
        $reflection = new Reflection(Scoped_3::class);
        $params = $reflection->getOptionalConstructorParams();
        $this->assertEquals($this->optionalConstructorParams, $params);
    }

    public function testGetSetters()
    {
        $reflection = new Reflection(Scoped_3::class);
        $params = $reflection->getSetters();
        $this->assertEquals($this->setters, $params);
    }

    public function testGetPublicProperties()
    {
        $reflection = new Reflection(Scoped_3::class);
        $params = $reflection->getPublicProperties();
        $this->assertEquals($this->publicProperties, $params);
    }

    public function testCreateInstance()
    {
        $reflection = new Reflection(Scoped_3::class);
        $instance = $reflection->createInstance($this->params);
        $this->checkInstance($instance, Scoped_3::class, null, []);
    }

    public function testGetClass()
    {
        $reflection = new Reflection(Scoped_3::class);
        $this->assertEquals(Scoped_3::class, $reflection->getClass());
    }

    public function testInvalidClassException()
    {
        $this->expectException(ReflectionException::class);
        $this->expectExceptionCode(ReflectionException::INVALID_CLASS);
        new Reflection($this->notExistedClass);
    }
}
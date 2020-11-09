<?php declare(strict_types=1);

namespace rkwadriga\simpledi\tests;

use rkwadiga\simpledi\Container;
use rkwadiga\simpledi\ContainerItem;
use rkwadiga\simpledi\exception\ContainerItemException;
use rkwadiga\simpledi\exception\PhpDocReaderException;
use rkwadiga\simpledi\Reflection;
use rkwadriga\simpledi\tests\mock\Interface1;
use rkwadriga\simpledi\tests\mock\InvalidBehaviorClass;
use rkwadriga\simpledi\tests\mock\Scoped_3;
use ReflectionClass;

/**
 * Command ./run ContainerItemTest
 */
class ContainerItemTest extends AbstractTest
{
    public function testClassItem()
    {
        $class = Scoped_3::class;
        $behavior = Container::SCOPED;
        $checkParams = $this->getParams($behavior);
        $checkReflection = new Reflection($class);
        $checkReflection->getConfiguration();
        $IDClassItem = new ContainerItem($class);

        $this->assertEquals($class, $IDClassItem->id);
        $this->assertEquals($class, $IDClassItem->class);
        $this->assertEquals($behavior, $IDClassItem->behavior);
        $this->assertNull($IDClassItem->value);
        $this->assertEquals($checkParams, $IDClassItem->config);
        $this->assertFalse($IDClassItem->isInstalled);
        $this->assertFalse($IDClassItem->isScalar);
        $this->assertEquals($checkReflection, $IDClassItem->reflection);
        $this->assertNull($IDClassItem->installer);

        $arrayIDClassItem = new ContainerItem(['class' => $class]);
        unset($arrayIDClassItem->config['class']);
        $this->assertEquals($IDClassItem, $arrayIDClassItem);

        $configClassItem = new ContainerItem('test_class', ['class' => $class]);
        $IDClassItem->id = 'test_class';
        unset($configClassItem->config['class']);
        $this->assertEquals($IDClassItem, $configClassItem);

        $stringClassItem = new ContainerItem('test_class', $class);
        $IDClassItem->id = 'test_class';
        unset($stringClassItem->config['class']);
        $this->assertEquals($IDClassItem, $stringClassItem);

        $interfaceConfigClassItem = new ContainerItem(Interface1::class, ['class' => $class]);
        $IDClassItem->id = Interface1::class;
        unset($interfaceConfigClassItem->config['class']);
        $this->assertEquals($IDClassItem, $interfaceConfigClassItem);

        $interfaceStringClassItem = new ContainerItem(Interface1::class, $class);
        $IDClassItem->id = Interface1::class;
        unset($interfaceStringClassItem->config['class']);
        $this->assertEquals($IDClassItem, $interfaceStringClassItem);
    }

    public function testClosureItem()
    {
        $class = Scoped_3::class;
        $behavior = Container::SCOPED;
        $closure = $this->getClosure($class);
        $checkParams = $this->getParams($behavior);
        $checkReflection = new Reflection($class);
        $checkReflection->getConfiguration();

        $IDContainerItem = new ContainerItem('closure_id', $closure);
        $this->assertEquals('closure_id', $IDContainerItem->id);
        $this->assertNull($IDContainerItem->class);
        $this->assertNull($IDContainerItem->behavior);
        $this->assertNull($IDContainerItem->value);
        $this->assertEquals([], $IDContainerItem->config);
        $this->assertFalse($IDContainerItem->isInstalled);
        $this->assertFalse($IDContainerItem->isScalar);
        $this->assertNull($IDContainerItem->reflection);
        $this->assertEquals($closure, $IDContainerItem->installer);

        $ClassContainerItem = new ContainerItem($class, $closure);
        $this->assertEquals($class, $ClassContainerItem->id);
        $this->assertEquals($class, $ClassContainerItem->class);
        $this->assertEquals($behavior, $ClassContainerItem->behavior);
        $this->assertNull($ClassContainerItem->value);
        $this->assertEquals($checkParams, $ClassContainerItem->config);
        $this->assertFalse($ClassContainerItem->isInstalled);
        $this->assertFalse($ClassContainerItem->isScalar);
        $this->assertEquals($checkReflection, $ClassContainerItem->reflection);
        $this->assertEquals($closure, $ClassContainerItem->installer);

        $closureItem = new ContainerItem($closure);
        $this->assertNull($closureItem->id);
        $this->assertNull($closureItem->class);
        $this->assertNull($closureItem->behavior);
        $this->assertNull($closureItem->value);
        $this->assertEquals([], $closureItem->config);
        $this->assertFalse($closureItem->isInstalled);
        $this->assertFalse($closureItem->isScalar);
        $this->assertNull($closureItem->reflection);
        $this->assertEquals($closure, $closureItem->installer);

        $closureConfigIDItem = new ContainerItem($closure, ['id' => 'closure_id']);
        $this->assertEquals('closure_id', $closureConfigIDItem->id);
        $this->assertNull($closureConfigIDItem->class);
        $this->assertNull($closureConfigIDItem->behavior);
        $this->assertNull($closureConfigIDItem->value);
        $this->assertEquals(['id' => 'closure_id'], $closureConfigIDItem->config);
        $this->assertFalse($closureConfigIDItem->isInstalled);
        $this->assertFalse($closureConfigIDItem->isScalar);
        $this->assertNull($closureConfigIDItem->reflection);
        $this->assertEquals($closure, $closureConfigIDItem->installer);

        $closureConfigNotClassItem = new ContainerItem($closure, ['class' => 'closure_id']);
        $this->assertEquals('closure_id', $closureConfigNotClassItem->id);
        $this->assertNull($closureConfigNotClassItem->class);
        $this->assertNull($closureConfigNotClassItem->behavior);
        $this->assertNull($closureConfigNotClassItem->value);
        $this->assertEquals(['class' => 'closure_id'], $closureConfigNotClassItem->config);
        $this->assertFalse($closureConfigNotClassItem->isInstalled);
        $this->assertFalse($closureConfigNotClassItem->isScalar);
        $this->assertNull($closureConfigNotClassItem->reflection);
        $this->assertEquals($closure, $closureConfigNotClassItem->installer);

        $checkParams = array_merge(['class' => $class], $checkParams);
        $closureConfigClassItem = new ContainerItem($closure, ['class' => $class]);
        $this->assertEquals($class, $closureConfigClassItem->id);
        $this->assertEquals($class, $closureConfigClassItem->class);
        $this->assertEquals($behavior, $closureConfigClassItem->behavior);
        $this->assertNull($closureConfigClassItem->value);
        $this->assertEquals($checkParams, $closureConfigClassItem->config);
        $this->assertFalse($closureConfigClassItem->isInstalled);
        $this->assertFalse($closureConfigClassItem->isScalar);
        $this->assertEquals($checkReflection, $closureConfigClassItem->reflection);
        $this->assertEquals($closure, $closureConfigClassItem->installer);

        unset($checkParams['class']);
        $checkParams = array_merge(['id' => $class], $checkParams);
        $closureConfigIDClassItem = new ContainerItem($closure, ['id' => $class]);
        $this->assertEquals($class, $closureConfigIDClassItem->id);
        $this->assertEquals($class, $closureConfigIDClassItem->class);
        $this->assertEquals($behavior, $closureConfigIDClassItem->behavior);
        $this->assertNull($closureConfigIDClassItem->value);
        $this->assertEquals($checkParams, $closureConfigIDClassItem->config);
        $this->assertFalse($closureConfigIDClassItem->isInstalled);
        $this->assertFalse($closureConfigIDClassItem->isScalar);
        $this->assertEquals($checkReflection, $closureConfigIDClassItem->reflection);
        $this->assertEquals($closure, $closureConfigIDClassItem->installer);
    }

    public function testScalarItem()
    {
        $stringItem = new ContainerItem('string_id', 'String value');
        $this->assertEquals('string_id', $stringItem->id);
        $this->assertNull($stringItem->class);
        $this->assertNull($stringItem->behavior);
        $this->assertEquals('String value', $stringItem->value);
        $this->assertEquals([], $stringItem->config);
        $this->assertTrue($stringItem->isInstalled);
        $this->assertTrue($stringItem->isScalar);
        $this->assertNull($stringItem->reflection);
        $this->assertNull($stringItem->installer);
    }

    public function testInvalidIDTypeExceptionNull()
    {
        $this->expectException(ContainerItemException::class);
        $this->expectExceptionCode(ContainerItemException::INVALID_ID_TYPE);
        new ContainerItem(null, $this->getParams(Container::SCOPED));
    }

    public function testInvalidIDTypeExceptionFalse()
    {
        $this->expectException(ContainerItemException::class);
        $this->expectExceptionCode(ContainerItemException::INVALID_ID_TYPE);
        new ContainerItem(false, $this->getParams(Container::SCOPED));
    }

    public function testInvalidIDTypeExceptionTrue()
    {
        $this->expectException(ContainerItemException::class);
        $this->expectExceptionCode(ContainerItemException::INVALID_ID_TYPE);
        new ContainerItem(true, $this->getParams(Container::SCOPED));
    }

    public function testInvalidIDTypeExceptionResource()
    {
        $reflection = new ReflectionClass(Scoped_3::class);
        $resource = fopen($reflection->getFileName(), 'r');
        $this->expectException(ContainerItemException::class);
        $this->expectExceptionCode(ContainerItemException::INVALID_ID_TYPE);
        new ContainerItem($resource, $this->getParams(Container::SCOPED));
        fclose($resource);
    }

    public function testInvalidIDTypeExceptionClosedResource()
    {
        $reflection = new ReflectionClass(Scoped_3::class);
        $resource = fopen($reflection->getFileName(), 'r');
        fclose($resource);
        $this->expectException(ContainerItemException::class);
        $this->expectExceptionCode(ContainerItemException::INVALID_ID_TYPE);
        new ContainerItem($resource, $this->getParams(Container::SCOPED));
    }

    public function testMissedClassException()
    {
        $this->expectException(ContainerItemException::class);
        $this->expectExceptionCode(ContainerItemException::MISSED_CLASS);
        new ContainerItem(['id' => Scoped_3::class]);
    }

    public function testMissedImplementationException()
    {
        $this->expectException(ContainerItemException::class);
        $this->expectExceptionCode(ContainerItemException::MISSED_IMPLEMENTATION);
        new ContainerItem(Interface1::class, ['id' => Scoped_3::class]);
    }

    public function testInvalidConfigBehaviorException()
    {
        $this->expectException(ContainerItemException::class);
        $this->expectExceptionCode(ContainerItemException::INVALID_BEHAVIOR);
        new ContainerItem(Scoped_3::class, ['behavior' => 'INVALID_BEHAVIOR']);
    }

    public function testInvalidClassBehaviorException()
    {
        $this->expectException(PhpDocReaderException::class);
        $this->expectExceptionCode(PhpDocReaderException::INVALID_BEHAVIOR);
        new ContainerItem(InvalidBehaviorClass::class);
    }
}
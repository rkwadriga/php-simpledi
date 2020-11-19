<?php declare(strict_types=1);

namespace rkwadriga\simpledi\tests;

use rkwadiga\simpledi\Container;
use rkwadiga\simpledi\ContainerItem;
use rkwadiga\simpledi\Reflection;
use rkwadriga\simpledi\tests\mock\Singleton_3;
use rkwadriga\simpledi\tests\mock\Transient_3;
use rkwadriga\simpledi\tests\mock\Scoped_3;

/**
 * Command ./run ContainerTest
 */
class ContainerTest extends AbstractTest
{
    public function testSetScalar()
    {
        $container = new Container();
        $id = 'string_scalar';
        $value = 'String_Scalar_Value';
        $container->set($id, $value);
        $this->checkScalarContainerItem($container, $id, $value);

        $id = 'int_scalar';
        $value = 123;
        $container->set($id, $value);
        $this->checkScalarContainerItem($container, $id, $value);

        $id = 'float_scalar';
        $value = 3.14;
        $container->set($id, $value);
        $this->checkScalarContainerItem($container, $id, $value);

        $id = 'array_scalar';
        $value = [1, 2, 3];
        $container->set($id, $value);
        $this->checkScalarContainerItem($container, $id, $value);

        $id = 'object_scalar';
        $value = $this->createObject(Singleton_3::class);
        $container->set($id, $value);
        $this->checkScalarContainerItem($container, $id, $value);
    }

    public function testSetObject()
    {
        $container = new Container();
        $id = Singleton_3::class;
        $container->set($id, $this->createObject($id));
        $this->checkObjectContainerItem($container, $id);

        $id = Transient_3::class;
        $container->set($id, $this->createObject($id));
        $this->checkObjectContainerItem($container, $id);

        $id = Scoped_3::class;
        $container->set($id, $this->createObject($id));
        $this->checkObjectContainerItem($container, $id);
    }

    public function testSetClosure()
    {
        $container = new Container();
        $id = Singleton_3::class;
        $closure = $this->getClosure($id);
        $reflection = new Reflection($id);
        $reflection->getConfiguration();
        $container->set($id, $closure);

        $item = $this->getContainerItem($container, $id);
        $this->assertEquals($id, $item->id);
        $this->assertEquals($id, $item->class);
        $this->assertEquals(Container::SINGLETON, $item->behavior);
        $this->assertFalse($item->isInstalled);
        $this->assertFalse($item->isScalar);
        $this->assertNull($item->value);
        $this->assertEquals($this->getParams(Container::SINGLETON), $item->config);
        $this->assertEquals($reflection, $item->reflection);
        $this->assertEquals($item->installer, $closure);
    }

    public function testContainerConfiguration()
    {
        $configuration = $this->getContainerConfiguration();
        $container = new Container($configuration);

        $this->checkScalarContainerItem($container, 'string_scalar', $configuration['string_scalar']);
        $this->checkScalarContainerItem($container, 'int_scalar', $configuration['int_scalar']);
        $this->checkScalarContainerItem($container, 'float_scalar', $configuration['float_scalar']);
        $this->checkScalarContainerItem($container, 'array_scalar', $configuration['array_scalar']);
        $this->checkScalarContainerItem($container, 'object_scalar', $configuration['object_scalar']);
        $this->checkObjectContainerItem($container, 'singleton_item', Singleton_3::class);
        $this->checkObjectContainerItem($container, 'transient_item', Transient_3::class, null, $configuration['transient_item']);

        $id = Scoped_3::class;
        $closure = $this->getClosure($id);
        $reflection = new Reflection($id);
        $reflection->getConfiguration();

        $item = $this->getContainerItem($container, $id);
        $this->assertEquals($id, $item->id);
        $this->assertEquals($id, $item->class);
        $this->assertEquals(Container::SCOPED, $item->behavior);
        $this->assertFalse($item->isInstalled);
        $this->assertFalse($item->isScalar);
        $this->assertNull($item->value);
        $this->assertEquals($this->getParams(Container::SCOPED), $item->config);
        $this->assertEquals($reflection, $item->reflection);
        $this->assertEquals($item->installer, $closure);
    }


    private function getContainerItem(Container $container, string $id) : ContainerItem
    {
        $itemsContainer = $this->getPrivateProperty($container, 'container');
        $this->assertArrayHasKey($id, $itemsContainer);
        $this->assertInstanceOf(ContainerItem::class, $itemsContainer[$id]);
        return $itemsContainer[$id];
    }

    private function checkScalarContainerItem(Container $container, string $id, $value) : ContainerItem
    {
        $item = $this->getContainerItem($container, $id);
        $this->assertTrue($item->isScalar);
        $this->assertTrue($item->isInstalled);
        $this->assertNull($item->class);
        $this->assertEquals($id, $item->id);
        $this->assertEquals($value, $item->value);
        return $item;
    }

    private function checkObjectContainerItem(Container $container, string $id, ?string $class = null, ?string $behavior = null, array $params = [])
    {
        if ($class === null) {
            $class = $id;
        }
        if ($behavior === null) {
            if (strpos($class, Container::TRANSIENT) !== false) {
                $behavior = Container::TRANSIENT;
            } elseif (strpos($class, Container::SCOPED) !== false) {
                $behavior = Container::SCOPED;
            } else {
                $behavior = Container::SINGLETON;
            }
        }
        if (empty($params)) {
            $params = $this->getParams($behavior);
        }

        $reflection = new Reflection($class);
        $reflection->getConfiguration();

        $item = $this->getContainerItem($container, $id);
        $this->assertEquals($id, $item->id);
        $this->assertEquals($class, $item->class);
        $this->assertEquals($behavior, $item->behavior);
        $this->assertFalse($item->isInstalled);
        $this->assertFalse($item->isScalar);
        $this->assertNull($item->installer);
        $this->assertNull($item->value);
        $this->assertEquals($params, $item->config);
        $this->assertEquals($reflection, $item->reflection);
    }
}
<?php declare(strict_types=1);

namespace rkwadriga\simpledi\tests;

use rkwadiga\simpledi\Container;
use rkwadiga\simpledi\behaviors\Factory;
use rkwadiga\simpledi\behaviors\Scoped;
use rkwadiga\simpledi\behaviors\Singleton;
use rkwadiga\simpledi\behaviors\Transient;
use rkwadiga\simpledi\exception\ContainerException;

/**
 * Command ./run BehaviorsFactoryTest
 */
class BehaviorsFactoryTest extends AbstractTest
{
    public function testGetBehavior()
    {
        $this->assertInstanceOf(Singleton::class, Factory::getBehavior(Container::SINGLETON));
        $this->assertInstanceOf(Transient::class, Factory::getBehavior(Container::TRANSIENT));
        $this->assertInstanceOf(Scoped::class, Factory::getBehavior(Container::SCOPED));
    }

    public function testBehaviorNotImplemented()
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionCode(ContainerException::BEHAVIOR_NOT_IMPLEMENTED);
        Factory::getBehavior('NOT_IMPLEMENTED_BEHAVIOR');
    }
}
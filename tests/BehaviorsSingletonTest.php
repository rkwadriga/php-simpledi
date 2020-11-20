<?php declare(strict_types=1);

namespace rkwadriga\simpledi\tests;

use rkwadiga\simpledi\behaviors\Singleton;
use rkwadiga\simpledi\Container;
use rkwadriga\simpledi\tests\mock\Scoped_3;

/**
 * Command ./run BehaviorsSingletonTest
 */
class BehaviorsSingletonTest extends AbstractTest
{
    public function testScalarValue()
    {
        $behavior = new Singleton(Container::SINGLETON);

        $this->checkBehaviorValue($behavior, 'scalar_string_item', 'Scalar String Value');
        $this->checkBehaviorValue($behavior, 'scalar_int_item', 1234);
        $this->checkBehaviorValue($behavior, 'scalar_float_item', 3.14);
        $this->checkBehaviorValue($behavior, 'scalar_array_item', [1, 2, 3]);
        $this->checkBehaviorValue($behavior, 'scalar_object_item', $this->createObject(Scoped_3::class));
    }
}
<?php declare(strict_types=1);

namespace rkwadriga\simpledi\tests;

use rkwadiga\simpledi\ContainerItem;
use rkwadiga\simpledi\exception\ObjectCreatorException;
use rkwadiga\simpledi\ObjectCreator;
use rkwadiga\simpledi\Reflection;
use rkwadriga\simpledi\tests\mock\Scoped_3;
use rkwadriga\simpledi\tests\mock\Singleton_3;
use rkwadriga\simpledi\tests\mock\Transient_3;

/**
 * Command ./run ObjectCreatorTest
 */
class ObjectCreatorTest extends AbstractTest
{
    public function testAllowsNull()
    {
        $creator = new ObjectCreator();
        $type = '?string';
        $result = $this->callPrivateMethod($creator, 'allowsNull', [&$type]);
        $this->assertTrue($result);
        $this->assertEquals('string', $type);

        $type = '?integer';
        $result = $this->callPrivateMethod($creator, 'allowsNull', [&$type]);
        $this->assertTrue($result);
        $this->assertEquals('integer', $type);

        $type = '?float';
        $result = $this->callPrivateMethod($creator, 'allowsNull', [&$type]);
        $this->assertTrue($result);
        $this->assertEquals('float', $type);

        $type = '?array';
        $result = $this->callPrivateMethod($creator, 'allowsNull', [&$type]);
        $this->assertTrue($result);
        $this->assertEquals('array', $type);

        $type = 'string';
        $result = $this->callPrivateMethod($creator, 'allowsNull', [&$type]);
        $this->assertFalse($result);
        $this->assertEquals('string', $type);

        $type = 'integer';
        $result = $this->callPrivateMethod($creator, 'allowsNull', [&$type]);
        $this->assertFalse($result);
        $this->assertEquals('integer', $type);

        $type = 'float';
        $result = $this->callPrivateMethod($creator, 'allowsNull', [&$type]);
        $this->assertFalse($result);
        $this->assertEquals('float', $type);

        $type = 'array';
        $result = $this->callPrivateMethod($creator, 'allowsNull', [&$type]);
        $this->assertFalse($result);
        $this->assertEquals('array', $type);
    }

    public function testCheckType()
    {
        $creator = new ObjectCreator();
        $param = 'paramName';

        $expectedType = 'string';
        $value = 'Test string';
        $this->callPrivateMethod($creator, 'checkType', [$expectedType, $param, $value]);

        $expectedType = 'int';
        $value = 123;
        $this->callPrivateMethod($creator, 'checkType', [$expectedType, $param, $value]);

        $expectedType = 'float';
        $value = 3.14;
        $this->callPrivateMethod($creator, 'checkType', [$expectedType, $param, $value]);

        $expectedType = 'array';
        $value = [3.14];
        $this->callPrivateMethod($creator, 'checkType', [$expectedType, $param, $value]);

        $expectedType = Singleton_3::class;
        $value = $this->createObject($expectedType);
        $this->callPrivateMethod($creator, 'checkType', [$expectedType, $param, $value]);

        $this->assertTrue(true);
    }

    public function testCreateConstructorParams()
    {
        $class = Singleton_3::class;
        $reflection = new Reflection($class);
        $creator = new ObjectCreator();
        list($privateParams, $publicParams) = $this->getRandomPrivateAndPublicParams();
        $checkParams = array_merge($privateParams, $publicParams);
        $constructorParams = $this->callPrivateMethod($creator, 'createConstructorParams', [$reflection, &$checkParams]);
        $this->assertEquals($publicParams, $checkParams);
        $this->assertEquals($privateParams, $constructorParams);
    }

    public function testCreateSetters()
    {
        $class = Singleton_3::class;
        $reflection = new Reflection($class);
        $creator = new ObjectCreator();
        list($privateParams, $publicParams) = $this->getRandomPrivateAndPublicParams();
        $checkParams = $previousCheckParams = array_merge($privateParams, $publicParams);
        $checkSetters = ['setPublicFloat' => $previousCheckParams['publicFloat']];
        unset($previousCheckParams['publicFloat']);
        $setters = $this->callPrivateMethod($creator, 'createSetters', [$reflection, &$checkParams]);
        $this->assertEquals($checkSetters, $setters);
        $this->assertEquals($previousCheckParams, $checkParams);
    }

    public function testCreatePublicProperties()
    {
        $class = Singleton_3::class;
        $reflection = new Reflection($class);
        $creator = new ObjectCreator();
        list($privateParams, $publicParams) = $this->getRandomPrivateAndPublicParams();
        $checkParams = array_merge($privateParams, $publicParams);
        $publicProperties = $this->callPrivateMethod($creator, 'createPublicProperties', [$reflection, &$checkParams]);
        $this->assertEquals($publicParams, $publicProperties);
        $this->assertEquals($privateParams, $checkParams);
    }

    public function testCreateFromClassAnnotation()
    {
        $class = Scoped_3::class;
        $creator = new ObjectCreator();
        $containerItem = new ContainerItem($class);

        $creator->create($containerItem);
        $this->checkInstance($containerItem->value, $class);
    }

    public function testCreateFromConfigurationArray()
    {
        $class = Transient_3::class;
        list($privateParams, $publicParams) = $this->getRandomPrivateAndPublicParams();
        $creator = new ObjectCreator();
        $containerItem = new ContainerItem($class, array_merge($privateParams, $publicParams));
        $creator->create($containerItem);
        $this->checkInstance($containerItem->value, $class, $privateParams, $publicParams);
    }

    public function testCreateClassInConfigurationArray()
    {
        $class = Transient_3::class;
        list($privateParams, $publicParams) = $this->getRandomPrivateAndPublicParams();
        $creator = new ObjectCreator();
        $containerItem = new ContainerItem(array_merge(['class' => $class], $privateParams, $publicParams));
        $creator->create($containerItem);
        $this->checkInstance($containerItem->value, $class, $privateParams, $publicParams);
    }

    public function testNotExistedClassException()
    {
        $creator = new ObjectCreator();
        $containerItem = new ContainerItem('item_id', ['itemParam' => 'itemVal']);
        $this->expectException(ObjectCreatorException::class);
        $this->expectExceptionCode(ObjectCreatorException::NOT_EXISTED_CLASS);
        $creator->create($containerItem);
    }

    public function testMissedRequiredConstructorParamsException()
    {
        $creator = new ObjectCreator();
        $containerItem = new ContainerItem(Singleton_3::class);
        $containerItem->config = array_diff_key($this->params, ['privateString' => null]);

        $this->expectException(ObjectCreatorException::class);
        $this->expectExceptionCode(ObjectCreatorException::MISSED_REQUIRED_CONSTRUCTOR_PARAM);
        $creator->create($containerItem);
    }

    public function testInvalidParamTypeException()
    {
        $creator = new ObjectCreator();
        $containerItem = new ContainerItem(Singleton_3::class);
        $containerItem->config = array_merge($this->params, ['privateString' => 123]);

        $this->expectException(ObjectCreatorException::class);
        $this->expectExceptionCode(ObjectCreatorException::INVALID_PARAM_TYPE);
        $creator->create($containerItem);
    }
}
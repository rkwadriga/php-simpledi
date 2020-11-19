<?php declare(strict_types=1);

namespace rkwadriga\simpledi\tests\mock;

use Closure;
use ReflectionClass;
use ReflectionProperty;
use rkwadiga\simpledi\Container;

trait ConfigurationTrait
{
    protected string $notExistedClass = 'rkwadriga\simpledi\tests\mock\InvalidClass';
    protected string $paramsString = 'privateString="Private string value", privateInt=123, privateArray={"param1": 1, "param2": 2, "param3": 3}, publicString="Public string string value", publicFloat=3.14';
    protected array $params = ['privateString' => "Private string value", 'privateInt' => 123, 'privateArray' => ['param1' => 1, 'param2' => 2, 'param3' => 3], 'publicString' => "Public string string value", "publicFloat" => 3.14];
    protected array $requiredConstructorParams = ['privateString' => 'string', 'privateInt' => 'int'];
    protected array $optionalConstructorParams = ['privateArray' => '?array'];
    protected array $setters = ['publicFloat' => 'float'];
    protected array $publicProperties = ['publicString' => 'string', 'publicFloat' => 'float'];

    protected function getParamsString(string $behavior) : string
    {
        return "/**\n * Class {$behavior}_3\n * @package rkwadriga\\simpledi\\tests\\mock\n * @{$behavior}({$this->paramsString})\n*/";
    }
    protected function getParams(string $behavior) : array
    {
        return array_merge(['behavior' => $behavior], $this->params);
    }
    public function getRequiredConstructorParams()
    {
        return array_intersect_key($this->params, $this->requiredConstructorParams);
    }
    public function getOptionalConstructorParams()
    {
        return array_intersect_key($this->params, $this->optionalConstructorParams);
    }
    public function getSetters()
    {
        return array_intersect_key($this->params, $this->setters);
    }
    public function getPublicProperties()
    {
        return array_intersect_key($this->params, $this->publicProperties);
    }

    public function getClosure(string $class) : Closure
    {
        return function () use ($class) {
            return $this->createObject($class);
        };
    }

    public function getRandomParams(?string $behavior = null) : array
    {
        $randomParams = $this->params;
        foreach ($randomParams as $name => $value) {
            if (is_string($value)) {
                $randomParams[$name] = $value . '_' . rand(1000, 9999);
            } elseif (is_numeric($value)) {
                $randomParams[$name] = $value + rand(100, 999);
            } elseif (is_array($value)) {
                $randomParams[$name] = ['random_string' => 'Random_string_' . rand(1000, 9999), rand(100, 999)];
            }
        }
        return $behavior !== null ? array_merge(['behavior' => $behavior], $randomParams) : $randomParams;
    }

    public function getRandomPrivateAndPublicParams() : array
    {
        $privateParams = $publicParams = [];
        foreach ($this->getRandomParams() as $name => $value) {
            if (strpos($name, 'private') === 0) {
                $privateParams[$name] = $value;
            } else {
                $publicParams[$name] = $value;
            }
        }
        return [$privateParams, $publicParams];
    }

    public function getContainerConfiguration() : array
    {
        return [
            'string_scalar' => 'String value',
            'int_scalar' => 123,
            'float_scalar' => 3.14,
            'array_scalar' => ['param1' => 1, 'param2' => 2, 'param3' => 3],
            'object_scalar' => $this->createObject(Singleton_3::class),
            'singleton_item' => Singleton_3::class,
            'transient_item' => array_merge(['class' => Transient_3::class], $this->getRandomParams(Container::TRANSIENT)),
            Scoped_3::class => $this->getClosure(Scoped_3::class),
        ];
    }

    public function createObject(string $class, array $params = []) : object
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
<?php declare(strict_types=1);

namespace rkwadriga\simpledi\tests\mock;

use Closure;
use ReflectionClass;

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
            $reflect = new ReflectionClass($class);
            return $reflect->newInstanceArgs($this->params);
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
}
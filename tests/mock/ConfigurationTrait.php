<?php declare(strict_types=1);

namespace rkwadriga\simpledi\tests\mock;

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
}
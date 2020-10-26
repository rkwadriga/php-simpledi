<?php declare(strict_types=1);

namespace rkwadriga\simpledi\tests\mock;

trait ConfigurationTrait
{
    protected string $paramsString = 'privateString="Private string value", privateInt=123, privateArray={"param1": 1, "param2": 2, "param3": 3}, publicString="Public string string value", publicFloat=3.14';
    protected array $params = ['privateString' => "Private string value", 'privateInt' => 123, 'privateArray' => ['param1' => 1, 'param2' => 2, 'param3' => 3], 'publicString' => "Public string string value", "publicFloat" => 3.14];

    protected function getParamsString(string $behavior) : string
    {
        return "/**\n * Class {$behavior}_3\n * @package rkwadriga\\simpledi\\tests\\mock\n * @{$behavior}({$this->paramsString})\n*/";
    }
    protected function getParams(string $behavior) : array
    {
        return array_merge(['behavior' => $behavior], $this->params);
    }
}
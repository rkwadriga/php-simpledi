<?php declare(strict_types=1);

namespace rkwadriga\simpledi\tests\mock;

/**
 * Class Transient_3
 * @package rkwadriga\simpledi\tests\mock
 * @Transient(privateString="Private string value", privateInt=123, privateArray={"param1": 1, "param2": 2, "param3": 3}, publicString="Public string string value", publicFloat=3.14)
 */
class Transient_3
{
    private string $privateString;
    private int $privateInt;
    private ?array $privateArray;
    public ?string $publicString;
    public ?float $publicFloat;

    public function __construct(string $privateString, int $privateInt, ?array $privateArray = null)
    {
        $this->privateString = $privateString;
        $this->privateInt = $privateInt;
        $this->privateArray = $privateArray;
        $this->publicString = null;
        $this->publicFloat = null;
    }

    public function setPublicFloat(float $float) : void
    {
        $this->publicFloat = $float;
    }
}
<?php declare(strict_types=1);

namespace rkwadriga\simpledi\tests\mock;

/**
 * Class Singleton_3
 * @package rkwadriga\simpledi\tests\mock
 * @Singleton(privateString="Private string value", privateInt=123, privateArray=[1, 2, 3], publicString="Public string string value", publicFloat=3.14)
 */
class Singleton_3
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
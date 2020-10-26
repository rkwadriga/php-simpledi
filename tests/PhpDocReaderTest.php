<?php declare(strict_types=1);

namespace rkwadriga\simpledi\tests;

use rkwadiga\simpledi\Container;
use rkwadiga\simpledi\PhpDocReader;
use rkwadriga\simpledi\tests\mock\ConfigurationTrait;
use rkwadiga\simpledi\exception\PhpDocReaderException;

/**
 * Command ./run PhpDocReaderTest
 */
class PhpDocReaderTest extends AbstractTest
{
    use ConfigurationTrait;

    public function testRead()
    {
        $reader = new PhpDocReader();
        $params = $reader->read($this->getParamsString(Container::TRANSIENT));
        $checkParams = array_merge(['behavior' => Container::TRANSIENT], $this->params);
        $this->assertEquals($checkParams, $params);
    }

    public function testInvalidBehaviorException()
    {
        $reader = new PhpDocReader();
        $this->expectException(PhpDocReaderException::class);
        $this->expectExceptionCode(PhpDocReaderException::INVALID_BEHAVIOR);
        $reader->read($this->getParamsString('INVALID_BEHAVIOR'));
    }

    public function testInvalidFormatException()
    {
        $reader = new PhpDocReader();
        $paramsString = str_replace('(', '{', $this->getParamsString(Container::TRANSIENT));
        $this->expectException(PhpDocReaderException::class);
        $this->expectExceptionCode(PhpDocReaderException::INVALID_FORMAT);
        $reader->read($paramsString);
    }

    public function testInvalidJsonException()
    {
        $reader = new PhpDocReader();
        $paramsString = str_replace('{', '{{', $this->getParamsString(Container::TRANSIENT));
        $this->expectException(PhpDocReaderException::class);
        $this->expectExceptionCode(PhpDocReaderException::INVALID_JSON);
        $reader->read($paramsString);
    }
}
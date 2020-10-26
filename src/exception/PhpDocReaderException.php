<?php declare(strict_types=1);

namespace rkwadiga\simpledi\exception;

class PhpDocReaderException extends BaseException
{
    const INVALID_BEHAVIOR = 1001;
    const INVALID_FORMAT = 1002;
    const INVALID_JSON = 1003;

    public string $name = 'PHP Doc Reader Exception';
}
<?php declare(strict_types=1);

namespace rkwadiga\simpledi\exception;

class ReflectionException extends BaseException
{
    const INVALID_CLASS = 2001;

    public string $name = 'Reflection Exception';
}
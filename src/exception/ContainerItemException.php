<?php declare(strict_types=1);

namespace rkwadiga\simpledi\exception;

class ContainerItemException extends BaseException
{
    const MISSED_CLASS = 3001;
    const INVALID_ID_TYPE = 3002;
    const MISSED_IMPLEMENTATION = 3003;
    const INVALID_BEHAVIOR = 3004;

    public string $name = 'Container Item Exception';
}
<?php declare(strict_types=1);

namespace rkwadiga\simpledi\exception;

class ContainerException extends BaseException
{
    const INVALID_ITEM_ID = 5001;
    const ITEM_NOT_FOUND = 5002;
    const BEHAVIOR_NOT_IMPLEMENTED = 5003;

    public string $name = 'Container Exception';
}
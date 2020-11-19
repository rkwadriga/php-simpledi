<?php declare(strict_types=1);

namespace rkwadiga\simpledi\exception;

class ContainerException extends BaseException
{
    const INVALID_ITEM_ID = 5001;

    public string $name = 'Container Exception';
}
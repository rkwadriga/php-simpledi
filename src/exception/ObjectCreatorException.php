<?php declare(strict_types=1);

namespace rkwadiga\simpledi\exception;

class ObjectCreatorException extends BaseException
{
    const NOT_EXISTED_CLASS = 4001;
    const MISSED_REQUIRED_CONSTRUCTOR_PARAM = 4002;
    const INVALID_PARAM_TYPE = 4003;

    public string $name = "Object Creator Exception";
}
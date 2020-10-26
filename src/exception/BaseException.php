<?php declare(strict_types=1);

namespace rkwadiga\simpledi\exception;

use \Exception;

abstract class BaseException extends Exception
{
    public string $name = "Simple DI exception";
}
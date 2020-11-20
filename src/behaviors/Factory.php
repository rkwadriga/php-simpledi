<?php declare(strict_types=1);

namespace rkwadiga\simpledi\behaviors;

use rkwadiga\simpledi\exception\ContainerException;

class Factory
{
    public static function getBehavior(string $name) : AbstractBehavior
    {
        $class = __NAMESPACE__ . '\\' . $name;
        if (!class_exists($class)) {
            throw new ContainerException("Behavior \"{$name}\" is not implemented", ContainerException::BEHAVIOR_NOT_IMPLEMENTED);
        }
        return new $class($name);
    }
}
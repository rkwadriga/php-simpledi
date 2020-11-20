<?php declare(strict_types=1);

namespace rkwadiga\simpledi\behaviors;

use rkwadiga\simpledi\ContainerItem;

abstract class AbstractBehavior
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param ContainerItem $item
     * @return mixed
     */
    abstract public function getItemValue(ContainerItem $item);
}
<?php declare(strict_types=1);

namespace rkwadiga\simpledi\behaviors;

use rkwadiga\simpledi\ContainerItem;

class Singleton extends AbstractBehavior
{
    public function getItemValue(ContainerItem $item)
    {
        if ($item->isScalar || $item->isInstalled) {
            return $item->value;
        }

        $item->isInstalled = true;
        return $item->value;
    }
}
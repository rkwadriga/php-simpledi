<?php declare(strict_types=1);

namespace rkwadiga\simpledi;

use rkwadiga\simpledi\behaviors\AbstractBehavior;
use rkwadiga\simpledi\behaviors\Factory;
use rkwadiga\simpledi\exception\ContainerException;

class Container
{
    // Behaviors
    const SINGLETON = 'Singleton';
    const TRANSIENT = 'Transient';
    const SCOPED = 'Scoped';

    /** @var ContainerItem[]  */
    private array $container;

    private ObjectCreator $objectCreator;

    /**
     * @var AbstractBehavior[]
     */
    private array $behaviors = [];

    public function __construct(array $config = [])
    {
        $this->container = [];
        foreach ($config as $id => $params) {
            if (!is_string($id)) {
                if (isset($config['id']) && is_string($config['id'])) {
                    $id = $config['id'];
                } elseif (isset($config['class']) && is_string($config['class'])) {
                    $id = $config['class'];
                } else {
                    throw new ContainerException('Container item ID must be a string', ContainerException::INVALID_ITEM_ID);
                }
            }
            $this->set($id, $params);
        }
        $this->objectCreator = new ObjectCreator();
    }

    /**
     * @param string $id
     * @param mixed $params
     * @throws exception\ContainerItemException
     */
    public function set(string $id, $params = []) : void
    {
        $item = new ContainerItem($id, $params);
        $this->container[$item->id] = $item;
    }

    /**
     * @param string $id
     * @param ?string $behavior
     * @return mixed
     * @throws ContainerException
     * @throws exception\ContainerItemException
     */
    public function get(string $id, ?string $behavior = null)
    {
        if (!$this->has($id)) {
            if (class_exists($id)) {
                $this->set($id);
            } else {
                throw new ContainerException("Item \"{$id}\" not found in container", ContainerException::ITEM_NOT_FOUND);
            }
        }

        $item = $this->container[$id];
        if ($behavior === null) {
            $behavior = $item->behavior !== null ? $item->behavior : self::SINGLETON;
        }
        return $this->getBehavior($behavior)->getItemValue($item);
    }

    public function has(string $id) : bool
    {
        return isset($this->container[$id]);
    }

    /**
     * @param string $name
     * @return AbstractBehavior
     * @throws ContainerException
     */
    private function getBehavior(string $name) : AbstractBehavior
    {
        if (isset($this->behaviors[$name])) {
            return $this->behaviors[$name];
        }
        return $this->behaviors[$name] = Factory::getBehavior($name);
    }
}
<?php declare(strict_types=1);

namespace rkwadiga\simpledi;

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
}
<?php declare(strict_types=1);

namespace rkwadiga\simpledi;

use \Closure;
use rkwadiga\simpledi\exception\ContainerItemException;

class ContainerItem
{
    public ?string $id;
    public ?string $class;
    public ?string $behavior;
    /**
     * @var mixed
     */
    public $value;
    public array $config;
    public bool $isInstalled;
    public bool $isScalar;
    public ?Reflection $reflection;
    public ?Closure $installer;

    public function __construct($id, $config = [])
    {
        $this->id = null;
        $this->class = null;
        $this->behavior = null;
        $this->value = null;
        $this->isInstalled = false;
        $this->isScalar = false;
        $this->reflection = null;
        $this->installer = null;

        if (is_string($id)) {
            $this->id = $id;
        } elseif (is_numeric($id)) {
            $this->id = (string)$id;
        } elseif (is_null($id) || is_bool($id) || strpos(gettype($id), 'resource') === 0) {
            $tyre = gettype($id);
            throw new ContainerItemException("Invalid container item ID type: \"{$tyre}\"", ContainerItemException::INVALID_ID_TYPE);
        } elseif (is_array($id)) {
            $config = $id;
            if (!isset($config['class'])) {
                throw new ContainerItemException('Missed class in container item configuration array', ContainerItemException::MISSED_CLASS);
            }
            $this->id = $config['class'];
        } elseif (is_object($id) && !($id instanceof Closure)) {
            $this->value = $id;
            $this->id = $this->class = get_class($id);
            $this->config = $config;
            return;
        }

        if (is_array($config) && isset($config['class'])) {
            $this->class = $config['class'];
        } elseif (is_string($config)) {
            $this->class = $config;
        } else {
            $this->class = $this->id;
        }
        $this->config = is_array($config) ? $config : [];
        if ($id instanceof Closure) {
            $this->installer = $id;
        } elseif ($config instanceof Closure) {
            $this->installer = $config;
        }
        if ($this->id === null && isset($this->config['id']) && is_string($this->config['id'])) {
            $this->id = $this->config['id'];
        }
        if ($this->class === null && isset($this->config['class']) && is_string($this->config['class'])) {
            $this->class = $this->config['class'];
        }
        if ($this->class === null && $this->id !== null && class_exists($this->id)) {
            $this->class = $this->id;
        }
        if ($this->id === null) {
            $this->id = $this->class;
        }

        $this->isScalar = $this->installer === null && !class_exists($this->class) && !interface_exists($this->id);
        if ($this->isScalar) {
            $this->isInstalled = true;
            $this->value = $config;
            $this->class = null;
            return;
        } elseif ($this->class === null) {
            return;
        }

        if (interface_exists($this->id) && !class_exists($this->class)) {
            throw new ContainerItemException("Implementation class missed for interface \"{$this->id}\"", ContainerItemException::MISSED_IMPLEMENTATION);
        }

        if (class_exists($this->class)) {
            $this->reflection = new Reflection($this->class);
            $this->config = array_merge($this->reflection->getConfiguration(), $this->config);
        } else {
            $this->class = null;
        }

        if (isset($this->config['behavior'])) {
            if (!in_array($this->config['behavior'], [Container::SINGLETON, Container::TRANSIENT, Container::SCOPED])) {
                throw new ContainerItemException(sprintf('Invalid behavior: "%s"', $this->config['behavior']), ContainerItemException::INVALID_BEHAVIOR);
            }
            $this->behavior = $this->config['behavior'];
        }
    }
}
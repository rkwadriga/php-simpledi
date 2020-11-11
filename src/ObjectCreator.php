<?php declare(strict_types=1);

namespace rkwadiga\simpledi;

use rkwadiga\simpledi\exception\ObjectCreatorException;

class ObjectCreator
{
    public function create(ContainerItem $item) : void
    {
        if ($item->class === null || !class_exists($item->class)) {
            throw new ObjectCreatorException("Class \"{$item->class}\" does not exist", ObjectCreatorException::NOT_EXISTED_CLASS);
        }
        $params = $item->config;
        $constructorParams = $this->createConstructorParams($item->reflection, $params);

        $instance = $item->reflection->createInstance($constructorParams);
        foreach ($this->createSetters($item->reflection, $params) as $setter => $value) {
            call_user_func([$instance, $setter], $value);
        }
        foreach ($this->createPublicProperties($item->reflection, $params) as $property => $value) {
            $instance->$property = $value;
        }
        $item->value = $instance;
    }

    private function createConstructorParams(Reflection $reflection, array &$params) : array
    {
        $constructorParams = [];
        foreach ($reflection->getRequiredConstructorParams() as $param => $type) {
            if (!array_key_exists($param, $params)) {
                throw new ObjectCreatorException(sprintf('Required constructor param "%s" for class "%s" is missed', $param, $reflection->getClass()), ObjectCreatorException::MISSED_REQUIRED_CONSTRUCTOR_PARAM);
            }
            $value = $params[$param];
            $this->checkType($type, $param, $value);
            $constructorParams[$param] = $value;
            unset($params[$param]);
        }
        foreach ($reflection->getOptionalConstructorParams() as $param => $type) {
            $allowsNull = $this->allowsNull($type);
            if (!array_key_exists($param, $params)) {
                if ($allowsNull) {
                    $constructorParams[$param] = null;
                }
                continue;
            }
            $value = $params[$param];
            $this->checkType($type, $param, $value);
            $constructorParams[$param] = $value;
            unset($params[$param]);
        }
        return $constructorParams;
    }

    private function createSetters(Reflection $reflection, array &$params) : array
    {
        if (empty($params)) {
            return [];
        }
        $setters = [];
        foreach ($reflection->getSetters() as $param => $type) {
            $allowsNull = $this->allowsNull($type);
            if (!array_key_exists($param, $params)) {
                continue;
            }
            $value = $params[$param];
            if (!$allowsNull || $value !== null) {
                $this->checkType($type, $param, $value);
            }
            $setters['set' . ucfirst($param)] = $value;
            unset($params[$param]);
        }
        return $setters;
    }

    private function createPublicProperties(Reflection $reflection, array &$params) : array
    {
        if (empty($params)) {
            return [];
        }
        $publicProperties = [];
        foreach ($reflection->getPublicProperties() as $param => $type) {
            $allowsNull = $this->allowsNull($type);
            if (!array_key_exists($param, $params)) {
                continue;
            }
            $value = $params[$param];
            if (!$allowsNull || $value !== null) {
                $this->checkType($type, $param, $value);
            }
            $publicProperties[$param] = $value;
            unset($params[$param]);
        }
        return $publicProperties;
    }

    private function allowsNull(string &$type)
    {
        $allowsNull = strpos($type, '?') === 0;
        if ($allowsNull) {
            $type = substr($type, 1);
        }
        return $allowsNull;
    }

    /**
     * @param string $expectedType
     * @param string $param
     * @param mixed $value
     * @throws ObjectCreatorException
     */
    private function checkType(string $expectedType, string $param, $value) : void
    {
        $actualType = gettype($value);
        if ($actualType === 'integer') {
            $actualType = 'int';
        } elseif ($actualType === 'double') {
            $actualType = 'float';
        } elseif ($actualType === 'object') {
            $actualType = get_class($value);
        }
        if ($actualType !== $expectedType) {
            throw new ObjectCreatorException("Invalid param \"{$param}\" type. Expected: {$expectedType}, actual: {$actualType}", ObjectCreatorException::INVALID_PARAM_TYPE);
        }
    }
}
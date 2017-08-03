<?php

namespace jugger\model;

trait ModelAccessTrait
{
    public function __isset(string $name)
    {
        return $this->existsField($name);
    }

    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }

    public function __set(string $name, $value)
    {
        $this->setValue($name, $value);
    }

    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }

    public function __get(string $name)
    {
        return $this->getValue($name);
    }

    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    public function __unset(string $name)
    {
        $this->setValue($name, null);
    }

    public function offsetUnset($offset)
    {
        $this->__unset($offset);
    }
}

<?php

namespace jugger\model;

trait ModelAccessTrait
{
    public function __isset(string $name)
    {
        return $this->existsField($name);
    }

    public function __set(string $name, $value)
    {
        $this->setValue($name, $value);
    }

    public function __get(string $name)
    {
        return $this->getValue($name);
    }

    public function __unset(string $name)
    {
        $this->setValue($name, null);
    }
}

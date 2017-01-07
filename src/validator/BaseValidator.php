<?php

namespace jugger\model\validator;

use jugger\model\Model;

abstract class BaseValidator
{
    protected $_field;

    public function setField(Field $field)
    {
        $this->_field = $field;
    }

    public function getField()
    {
        return $this->_field;
    }

    abstract public function validate($value): bool;
}

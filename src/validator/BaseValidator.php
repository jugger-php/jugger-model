<?php

namespace jugger\model\validator;

use jugger\model\field\BaseField;

abstract class BaseValidator
{
    protected $_field;

    public function setField(BaseField $field)
    {
        $this->_field = $field;
    }

    public function getField(): BaseField
    {
        return $this->_field;
    }

    abstract public function validate($value): bool;
}

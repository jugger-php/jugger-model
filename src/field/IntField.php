<?php

namespace jugger\model\field;

class IntField extends BaseField
{
    protected function prepareValue($value)
    {
        if (is_scalar($value) || is_array($value)) {
            return (int) $value;
        }
        else {
            return 0;
        }
    }
}

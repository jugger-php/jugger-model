<?php

namespace jugger\model\field;

class FloatField extends BaseField
{
    protected function prepareValue($value)
    {
        if (is_scalar($value) || is_array($value)) {
            return (float) $value;
        }
        else {
            return 0;
        }
    }
}

<?php

namespace jugger\model\field;

class TextField extends BaseField
{
    protected function prepareValue($value)
    {
        if (is_bool($value)) {
            return $value ? "true" : "false";
        }
        elseif (is_scalar($value)) {
            return (string) $value;
        }
        else {
            return json_encode($value);
        }
    }
}

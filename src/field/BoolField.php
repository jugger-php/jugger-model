<?php

namespace jugger\model\field;

class BoolField extends BaseField
{
    protected function prepareValue($value)
    {
        return (bool) $value;
    }
}

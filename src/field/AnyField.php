<?php

namespace jugger\model\field;

class AnyField extends BaseField
{
    protected function prepareValue($value)
    {
        return $value;
    }
}

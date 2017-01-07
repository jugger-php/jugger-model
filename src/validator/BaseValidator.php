<?php

namespace jugger\model\validator;

use jugger\model\field\BaseField;

abstract class BaseValidator
{
    abstract public function validate($value): bool;
}

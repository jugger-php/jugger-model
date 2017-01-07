<?php

namespace jugger\model\validator;

abstract class BaseValidator
{
    abstract public function validate($value): bool;
}

<?php

namespace jugger\model\validator;

class RequireValidator extends BaseValidator
{
    public function __construct()
    {
        $this->message = "обязательно для заполнения";
    }

    public function validate($value): bool
    {
        return ! is_null($value);
    }
}

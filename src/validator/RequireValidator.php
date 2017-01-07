<?php

namespace jugger\model\validator;

class RequireValidator extends BaseValidator
{
    public function validate($value): bool
    {
        return ! is_null($value);
    }
}

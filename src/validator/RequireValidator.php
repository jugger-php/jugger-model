<?php

namespace jugger\model\validator;

class RequireValidator implements ValidatorInterface
{
    public function validate($value): bool
    {
        return ! is_null($value);
    }
}

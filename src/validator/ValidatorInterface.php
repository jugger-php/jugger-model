<?php

namespace jugger\model\validator;

interface ValidatorInterface
{
    public function validate($value): bool;
}

<?php

namespace jugger\model\validator;

class RegexpValidator implements ValidatorInterface
{
    protected $pattern;

    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    public function validate($value): bool
    {
        return !empty($value) && preg_match($this->pattern, $value) >= 1;
    }
}

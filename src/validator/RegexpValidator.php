<?php

namespace jugger\model\validator;

class RegexpValidator extends BaseValidator
{
    protected $pattern;

    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
        $this->message = "значение должно подходить под шаблон '{$pattern}'";
    }

    public function validate($value): bool
    {
        return !empty($value) && preg_match($this->pattern, $value) >= 1;
    }
}

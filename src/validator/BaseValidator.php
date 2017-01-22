<?php

namespace jugger\model\validator;

abstract class BaseValidator
{
    public $message;

    public function getMessage(): string
    {
        return $this->message ?? get_called_class();
    }

    abstract public function validate($value): bool;
}

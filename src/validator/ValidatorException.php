<?php

namespace jugger\model\validator;

class ValidatorException extends \Exception
{
    public $validator;

    public function __construct(BaseValidator $validator)
    {
        $this->validator = $validator;
    }
}

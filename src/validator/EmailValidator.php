<?php

namespace jugger\model\validator;

class EmailValidator extends RegexpValidator
{
    public function __construct()
    {
        parent::__construct('/^[0-9a-z\-]+\@[0-9a-z\-]+\.[a-z]+$/i');
        $this->message = "значение должно быть валидным email-адресом";
    }
}

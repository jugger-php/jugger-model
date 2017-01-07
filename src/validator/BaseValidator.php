<?php

namespace jugger\model\validator;

use jugger\model\Model;

abstract class BaseValidator
{
    protected $model;

    public function setModel(Model $model)
    {
        $this->model = $model;
    }

    abstract public function validate($value): bool;
}

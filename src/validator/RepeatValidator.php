<?php

namespace jugger\model\validator;

use jugger\model\Model;
use jugger\validator\BaseValidator;

class RepeatValidator extends BaseValidator
{
    protected $model;
    protected $fieldName;

    public function __construct(string $fieldName, Model $model)
    {
        $this->model = $model;
        $this->fieldName = $fieldName;
    }

    public function validate($value): bool
    {
        return $this->model->getField($this->fieldName)->getValue() == $value;
    }
}

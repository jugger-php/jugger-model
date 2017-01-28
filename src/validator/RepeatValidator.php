<?php

namespace jugger\model\validator;

use jugger\model\Model;

class RepeatValidator extends BaseValidator
{
    protected $model;
    protected $fieldName;

    public function __construct(string $fieldName, Model $model)
    {
        $this->model = $model;
        $this->fieldName = $fieldName;

        $fieldLabel = $this->model::getLabel($fieldName);
        $this->message = "значение должно совпадать с полем '{$fieldLabel}'";
    }

    public function validate($value): bool
    {
        return $this->model->getField($this->fieldName)->getValue() == $value;
    }
}

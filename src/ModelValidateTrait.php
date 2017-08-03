<?php

namespace jugger\model;

use jugger\validator\BaseValidator;

trait ModelValidateTrait
{
    private $_errors = [];

    public function validate(): bool
    {
        $this->_errors = [];
        $fields = $this->getFields();
        foreach ($fields as $name => $field) {
            if (!$field->validate()) {
                $this->_errors[$name] = $field->getError();
            }
        }
        return empty($this->_errors);
    }

    public function getErrors(): array
    {
        return $this->_errors;
    }

    public function getError(string $fieldName): ?BaseValidator
    {
        return $this->getErrors()[$fieldName] ?? null;
    }
}

<?php

namespace jugger\model;

/**
 * Трейт отвечающий за валидацию модели и работу с ошибками
 */
trait ModelValidateTrait
{
    private $_errors = [];

    public function validate(): bool
    {
        $this->_errors = [];
        $fields = $this->getFields();
        foreach ($fields as $name => $field) {
            if (!$field->validate()) {
                $label = static::getLabel($name);
                $error = $field->getError();
                $this->_errors[$name] = "Поле '{$label}': {$error}";
            }
        }
        return empty($this->_errors);
    }

    public function getErrors(): array
    {
        return $this->_errors;
    }

    public function getError(string $fieldName)
    {
        return $this->getErrors()[$fieldName] ?? null;
    }
}

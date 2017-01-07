<?php

namespace jugger\model;

/**
 * Трейт отвечающий за валидацию модели и работу с ошибками
 */
trait ModelValidateTrait
{
    private $_errors;

    public function validate()
    {
        $this->_errors = [];
        $fields = $this->getFields();
        foreach ($fields as $name => $field) {
            $field->setModel($this);
            if (!$field->validate()) {
                $this->_errors[$name] = $field->getError();
            }
        }
        return empty($this->_errors);
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    public function getError(string $fieldName)
    {
        return $this->getErrors()[$fieldName] ?? null;
    }
}

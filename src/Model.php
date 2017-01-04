<?php

namespace jugger\model;

use jugger\base\ArrayAccessTrait;

abstract class Model implements \ArrayAccess
{
    use ArrayAccessTrait;
    use ModelAccessTrait;

    private $_fields;
    private $_errors;

    public function __construct(array $values = [])
    {
        $this->setValues($values);
    }

    public function setValue(string $name, $value)
    {
        $this->getField($name)->setValue($value);
    }

    public function getValue(string $name)
    {
        return $this->getField($name)->getValue();
    }

    public function setValues(array $values)
    {
        $fields = $this->getFields();
        foreach ($fields as $name => $field) {
            if (isset($values[$name])) {
                $field->setValue($values[$name]);
            }
        }
    }

    public function getValues()
    {
        $values = [];
        foreach ($this->getFields() as $name => $field) {
            $values[$name] = $field->getValue();
        }
        return $values;
    }

    public function existsField(string $name)
    {
        $fields = $this->getFields();
        return isset($fields[$name]);
    }

    public function getField(string $name)
    {
        if ($this->existsField($name)) {
            return $this->getFields()[$name];
        }
        throw new \Exception("Field '{$name}' not exists");
    }

    public function getFields()
    {
        if (empty($this->_fields)) {
            $this->_fields = [];
            $schema = static::getSchema();
            foreach ($schema as $field) {
                $name = $field->getName();
                $this->_fields[$name] = $field;
            }
        }
        return $this->_fields;
    }

    abstract public static function getSchema();

    public function validate()
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

    public function getErrors()
    {
        return $this->_errors;
    }

    public function getError(string $fieldName)
    {
        return $this->getErrors()[$fieldName] ?? null;
    }
}

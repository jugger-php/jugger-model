<?php

namespace jugger\model;

use jugger\model\field\BaseField;

abstract class Model implements \ArrayAccess
{
    use ModelAccessTrait;
    use ModelValidateTrait;

    private $_fields;

    abstract public static function getSchema(): array;

    public function __construct(array $values = [])
    {
        $this->setValues($values);
        $this->init();
    }

    protected function init()
    {
        // pass
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

    public function getValues(): array
    {
        $values = [];
        foreach ($this->getFields() as $name => $field) {
            $values[$name] = $field->getValue();
        }
        return $values;
    }

    public function existsField(string $name): bool
    {
        $fields = $this->getFields();
        return isset($fields[$name]);
    }

    public function getField(string $name): BaseField
    {
        if ($this->existsField($name)) {
            return $this->getFields()[$name];
        }
        throw new \Exception("Field '{$name}' not exists");
    }

    public function getFields(): array
    {
        if (empty($this->_fields)) {
            $this->_fields = [];
            $schema = static::getSchema($this);
            foreach ($schema as $field) {
                $name = $field->getName();
                $this->_fields[$name] = $field;
            }
        }
        return $this->_fields;
    }
}

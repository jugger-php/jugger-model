<?php

namespace jugger\model\field;

use jugger\model\validator\ValidationTrait;

abstract class BaseField
{
    use ValidationTrait;

    public $model;

    protected $_name;
    protected $_value;

    public function __construct(array $config = [])
    {
        $this->initName($config['name'] ?? null);
        $this->setValue($config['value'] ?? null);
        $this->initValidators($config['validators'] ?? []);
        $this->init($config);
    }

    public function init(array $config)
    {
        // pass
    }

    public function initName($name)
    {
        if (is_string($name) && !empty($name)) {
            $this->_name = $name;
        }
        else {
            throw new \Exception("Property 'name' is required");
        }
    }

    protected function initValidators(array $validators)
    {
        foreach ($validators as $validator) {
            $this->addValidator($validator);
        }
    }

    public function setValue($value)
    {
        if (is_null($value)) {
            $this->_value = null;
        }
        else {
            $this->_value = $this->prepareValue($value);
        }
    }

    public function getValue()
    {
        return $this->_value;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function validate()
    {
        return $this->validateValue($this->getValue());
    }

    abstract protected function prepareValue($value);
}

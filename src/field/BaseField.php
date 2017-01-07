<?php

namespace jugger\model\field;

abstract class BaseField
{
    use FieldValidationTrait;

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

    /*
     * name
     */

    public function initName($name)
    {
        if (is_string($name) && !empty($name)) {
            $this->_name = $name;
        }
        else {
            throw new \Exception("Property 'name' is required");
        }
    }

    public function getName()
    {
        return $this->_name;
    }

    /*
     * validators
     */

    protected function initValidators(array $validators)
    {
        $this->addValidators($validators);
    }

    public function validate()
    {
        return $this->validateValue($this->getValue());
    }

    /*
     * value
     */

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

    abstract protected function prepareValue($value);
}

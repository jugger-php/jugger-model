<?php

namespace jugger\model\field;

abstract class BaseField
{
    use FieldValidationTrait;

    protected $_name;
    protected $_value;

    public function __construct(array $config = [])
    {
        $this->setName($config['name'] ?? null);

        $this->init($config);
        $this->setValue($config['value'] ?? null);
        $this->addValidators($config['validators'] ?? []);
    }

    public function init(array $config)
    {
        // pass
    }

    /*
     * name
     */

    protected function setName($name)
    {
        if (is_string($name) && !empty($name)) {
            $this->_name = $name;
        }
        else {
            throw new \Exception("Property 'name' is required");
        }
    }

    public function getName(): string
    {
        return $this->_name;
    }

    /*
     * validators
     */

    public function validate(): bool
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

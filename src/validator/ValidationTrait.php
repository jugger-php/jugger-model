<?php

namespace jugger\model\validator;

trait ValidationTrait
{
    protected $_error;
    protected $_validators;

    public function addValidator(ValidatorInterface $validator)
    {
        $this->_validators[] = $validator;
    }

    public function addValidators(array $validators)
    {
        foreach ($validators as $validator) {
            $this->addValidator($validator);
        }
    }

    public function getError()
    {
        return $this->_error;
    }

    public function getValidators()
    {
        return $this->_validators;
    }

    protected function validateValue($value)
    {
        $this->_error = null;
        foreach ($this->_validators as $validator) {
            if (!$validator->validate($value)) {
                $this->_error = get_class($validator);
                return false;
            }
        }
        return true;
    }
}
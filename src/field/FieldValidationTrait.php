<?php

namespace jugger\model\field;

use jugger\validator\BaseValidator;

trait FieldValidationTrait
{
    protected $_error;
    protected $_validators = [];

    public function addValidator(BaseValidator $validator): void
    {
        $this->_validators[] = $validator;
    }

    public function existValidator(string $validatorClass): bool
    {
        foreach ($this->_validators as $validator) {
            if ($validatorClass == get_class($validator)) {
                return true;
            }
        }
        return false;
    }

    public function addValidators(array $validators): void
    {
        foreach ($validators as $validator) {
            $this->addValidator($validator);
        }
    }

    public function getError(): ?BaseValidator
    {
        return $this->_error;
    }

    public function getValidators(): array
    {
        return $this->_validators;
    }

    protected function validateValue($value): bool
    {
        $this->_error = null;
        foreach ($this->_validators as $validator) {
            if (!$validator->validate($value)) {
                $this->_error = $validator;
                return false;
            }
        }
        return true;
    }
}

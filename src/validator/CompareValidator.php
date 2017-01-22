<?php

namespace jugger\model\validator;

class CompareValidator extends BaseValidator
{
    protected $operator;
    protected $compareValue;

    public function __construct(string $operator, $compareValue)
    {
        $this->message = "значение должно быть '{$operator} {$compareValue}'";
        $this->operator = $operator;
        $this->compareValue = $compareValue;
    }

    public function validate($a): bool
    {
        $b = $this->compareValue;
        switch ($this->operator) {
            case '==':
                return $a == $b;
            case '===':
                return $a === $b;
            case '!=':
                return $a != $b;
            case '!==':
                return $a !== $b;
            case '>':
                return $a > $b;
            case '>=':
                return $a >= $b;
            case '<':
                return $a < $b;
            case '<=':
                return $a <= $b;
            default:
                throw new \Exception("Invalide operator is '{$this->operator}'");
        }
    }
}

<?php

namespace jugger\model\validator;

class RangeValidator extends BaseValidator
{
    protected $min;
    protected $max;

    public function __construct(int $min, int $max = null)
    {
        $this->min = $min;
        $this->max = $max ?? 0;
        $this->message = "значение должно быть в диапазоне от {$min}". ($max ? " до {$max}" : "");
    }

    public function getMin()
    {
        return $this->min;
    }

    public function getMax()
    {
        return $this->max;
    }

    public function validate($value): bool
    {
        $max = $this->max;
        $length = $this->getLength($value);
        if ($max == 0) {
            $max = $length + 1;
        }
        return $this->min < $length && $length < $max;
    }

    protected function getLength($value)
    {
        if (is_null($value)) {
            $value = 0;
        }
        elseif (!is_scalar($value)) {
            throw new \Exception("How should I do it?");
        }
        elseif (is_float($value) || is_int($value)) {
            $value = (float) $value;
        }
        else {
            $value = mb_strlen((string) $value);
        }
        return $value;
    }
}

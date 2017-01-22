<?php

namespace jugger\model\field;

class DatetimeField extends BaseField
{
    protected $format;

    public function init(array $config)
    {
        $this->format = $config['format'] ?? 'Y-m-d H:i:s';
    }

    protected function prepareValue($value)
    {
        if (is_int($value) || is_float($value)) {
            $value = (int) $value;
            return $this->format == 'timestamp' ? $value : date($this->format, $value);
        }
        elseif (is_string($value)) {
            $date = new \DateTime($value);
            return $this->format == 'timestamp' ? $date->getTimestamp() : $date->format($this->format);
        }
        else {
            return null;
        }
    }

    public function getObject(): \DateTime
    {
        return \DateTime::createFromFormat($this->format, $this->getValue());
    }
}

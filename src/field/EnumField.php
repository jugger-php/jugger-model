<?php

namespace jugger\model\field;

class EnumField extends BaseField
{
    protected $_availValues;

    public function init(array $config)
    {
        parent::init($config);

        $values = $config['values'] ?? null;
        if (empty($values)) {
            throw new \Exception("Property 'values' is required for 'jugger\model\field\EnumField'");
        }
        else {
            $this->_availValues = (array) $values;
        }
    }

    protected function prepareValue($value)
    {
        if (array_search($value, $this->_availValues, true) === false) {
            return null;
        }
        else {
            return $value;
        }
    }

    public function getAvailableValues()
    {
        return $this->_availValues;
    }
}

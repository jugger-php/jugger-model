<?php

namespace jugger\model;

/**
 * Трейт отвечающий за обработку модели
 */
trait ModelHandlerTrait
{
    private $_handlers = [];

    public function handle(): bool
    {
        $handlers = array_merge(
            $this->_handlers,
            static::getHandlers()
        );
        foreach ($handlers as $handler) {
            if ($handler($this) === false) {
                return false;
            }
        }
        return true;
    }

    public static function getHandlers()
    {
        return [];
    }

    public function addHandler(\Closure $handler, bool $prepend = false)
    {
        if ($prepend) {
            array_unshift($this->_handlers, $handler);
        }
        else {
            array_push($this->_handlers, $handler);
        }
    }
}

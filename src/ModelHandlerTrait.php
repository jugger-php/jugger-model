<?php

namespace jugger\model;

use jugger\model\handler\HandleResult;
use jugger\model\handler\HandlerException;

/**
 * Трейт отвечающий за обработку модели
 */
trait ModelHandlerTrait
{
    private $_handlers;

    public function handle(): HandleResult
    {
        $handlers = $this->_handlers ?? static::getHandlers();
        foreach ($handlers as $handler) {
            try {
                $handler($this);
            }
            catch (HandlerException $ex) {
                return new HandleResult($ex);
            }
        }
        return new HandleResult();
    }

    public static function getHandlers(): array
    {
        return [];
    }

    public function addHandler(\Closure $handler, bool $prepend = false)
    {
        if (is_null($this->_handlers)) {
            $this->_handlers = static::getHandlers();
        }

        if ($prepend) {
            array_unshift($this->_handlers, $handler);
        }
        else {
            array_push($this->_handlers, $handler);
        }
    }
}

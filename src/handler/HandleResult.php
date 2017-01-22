<?php

namespace jugger\model\handler;

/**
 * Хранит в себе результаты обработки модели
 */
class HandleResult
{
    protected $_exception;

    public function __construct(HandlerException $exception = null)
    {
        $this->_exception = $exception;
    }

    public function getException(): HandlerException
    {
        return $this->_exception;
    }

    public function getMessage(): string
    {
        return $this->isSuccess() ? "success" : $this->_exception->getMessage();
    }

    public function isSuccess(): bool
    {
        return is_null($this->_exception);
    }
}

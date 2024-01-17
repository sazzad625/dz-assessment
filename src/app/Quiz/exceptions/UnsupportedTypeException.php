<?php


namespace App\Quiz\exceptions;


use Throwable;

class UnsupportedTypeException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

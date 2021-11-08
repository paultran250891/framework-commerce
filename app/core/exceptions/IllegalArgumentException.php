<?php

namespace app\core\exceptions;

class IllegalArgumentException extends \Exception
{
    protected $code = 416;
    public function __construct(string $message = null)
    {
        $this->message = $message ?? $this->message ;
    }
    protected $message = "URL da ton tai" ;
    
}

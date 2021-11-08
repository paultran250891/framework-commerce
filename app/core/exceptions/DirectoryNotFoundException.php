<?php
namespace app\core\exceptions;

class NotFoundException extends \Exception
{
    
    protected $code = 404;
    public function __construct(string $message = null)
    {
        $this->message = $message ?? $this->message ;
    }
    protected $message = "Page not Found" ;
    
            
}
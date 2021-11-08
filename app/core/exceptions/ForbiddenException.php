<?php
namespace app\core\exceptions;

class ForbiddenException extends \Exception
{
    protected $code = 403;
    public function __construct(string $message = null)
    {
        $this->message = $message ?? $this->message ;
    }
    protected $message = "Ban khong co quyen truy cap vao!!!" ;
    
}

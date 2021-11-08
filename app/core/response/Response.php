<?php

namespace app\core\response;

class Response
{
    public static function setStatusCode(int $code)
    {
        http_response_code($code);
    }

    public function redirect(string $url)
    {
        header('Location: ' . $url);
    }

    public function gethostname()
    {
        return $_SERVER['HTTP_REFERER'];
    }
}

<?php

namespace app\core\session;

use app\core\App;

class Session
{
    public const TOKEN = 'token';
    public  $hashToken = false;
    public const USER = 'user';
    public const CART = 'cart';

    public function __construct()
    {
        session_start();

        $this->token();
    }

    public function validateToken(string $token)
    {
        $this->get(self::TOKEN);
        return  password_verify($this->get(self::TOKEN), $token);
    }


    public function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    public function set(string $key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function remove(string $key)
    {
        unset($_SESSION[$key]);
    }

    public function token()
    {
        if (!$this->get(self::TOKEN)) {
            $this->set(self::TOKEN, rand(0, 10000));
        }
        $this->hashToken = password_hash($this->get(self::TOKEN), PASSWORD_DEFAULT);

        if (App::$app->request->path === '/token') {

            die(json_encode($this->hashToken));
        }
    }

    public function validateOneToken($token)
    {
        return ($this->hashToken === $token);
    }
}

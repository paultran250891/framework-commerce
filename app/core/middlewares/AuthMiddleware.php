<?php

namespace app\core\middlewares;

use app\core\App;
use app\core\exceptions\ForbiddenException;
use app\core\lib\Test;
use Google\Service\Texttospeech\Resource\Text;

class AuthMiddleware extends BaseMiddleware
{
    public array $actions = [];
    private string $token;
    public string $hashToken;
    private  $mess = false;
    private array $errors = [];

    private const USER = 'user';
    private const TOKEN = 'token';
    private const ADMIN = 'admin';

    public function __construct(array $action = [])
    {
        $this->actions = $action;
    }

    public function execute()
    {
        foreach ($this->errors as $key => $action) {
            if (!empty($action)) {
                if (!empty($this->actions[$key]) && in_array(App::$app->router->action, $this->actions[$key])) {
                    throw new ForbiddenException($this->mess[$key]);
                }
            }
        }
    }

    public function adminDashboard($action = [])
    {

        $this->checkSubmit($action);
        $user =  App::$app->session->get('user');

        if (!is_array($user) || $user['email'] !== 'paultran250891@gmail.com') {
            $this->mess[self::USER] = "ban khong co quyen dang nhap vao trang nay";
            $this->errors[self::ADMIN] = $action;
            $this->actions[self::ADMIN] = $action;
        }
    }

    public function checkSubmit($action = [])
    {
        $token = App::$app->request->getBody()['token'] ?? '';
        $token = App::$app->session->validateToken($token);
        if (!$token) {
            $this->mess[self::TOKEN] = "error Token";
            $this->errors[self::TOKEN] = $action;
            $this->actions[self::TOKEN] = $action;
        }
    }

    public function login($action = [])
    {

        if (!App::$app->login()) {
            $this->mess[self::USER] = "Ban chua dang nhap";
            $this->errors[self::USER] = $action;
            $this->actions[self::USER] = $action;
        }
    }
}

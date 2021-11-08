<?php

namespace app\core\controllers;

use app\core\exceptions\NotFoundException;

use app\core\middlewares\AuthMiddleware;
use app\core\App;
use app\core\lib\Test;
use Google\Service\Texttospeech\Resource\Text;

abstract class ControllerApi
{
    private array $result = [];
    private const RESPONSE = 'response';
    private const MESSAGE = 'message';
    private const CODE  = 'code';
    public static array $getMess = [
        200 => 'success',
        404 => 'du lieu rong',
        500 => 'loi he thong',
        403 => 'ban khong co quyen truy cap',
        401 => 'du lieu error',
        412 => 'du lieu goi khong hop le'
    ];

    abstract protected function actionsMiddle(): array;
    abstract function setResult(): array;

    public function action()
    {
        return App::$app->router->action;
    }

    private function setResponse()
    {
        $this->result[self::CODE] = $this->setResult()[1] ?? 200;
        $this->result[self::MESSAGE] = $this->setResult()[2] ?? null;
        $this->result[self::RESPONSE] = $this->setResult()[0] ?? [];
    }

    private function handleResult()
    {
        $this->setResponse();
        $this->result[self::MESSAGE] =
            $this->result[self::MESSAGE] ??
            self::$getMess[$this->result[self::CODE]];
        if (!array_key_exists($this->result[self::CODE], self::$getMess)) {
            throw new NotFoundException('statuscode khong dung');
        }
    }

    public function middleware()
    {
        $submit = $this->actionsMiddle()['token'] ?? [];
        $login = $this->actionsMiddle()['login'] ?? [];
        $admin = $this->actionsMiddle()['admin'] ?? [];
        $middle = new AuthMiddleware();

        $middle->checkSubmit($submit);
        $middle->login($login);
        $middle->adminDashboard($admin);
        $middle->execute();
    }

    public function __destruct()
    {
        // ob_end_clean();
        $this->middleware();
        $this->handleResult();


        echo json_encode($this->result);
    }
}

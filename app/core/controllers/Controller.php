<?php

namespace app\core\controllers;

use app\core\exceptions\NotFoundException;
use app\core\view\View;

class Controller
{
    public string $res = 'response';
    public string $code = 'code';
    public string $msg  = "";
    public array $result = [];
    public string $title;
    public string $layout;
    protected string $action;


    public function render($content = null, $only = false)
    {
        $view = new View();
        $view->title = $this->title ?? $view->title;
        $view->layout = $this->layout ?? $view->layout;
        echo ($only) ?  $view->file($content) : $view->render($content);

    }

}

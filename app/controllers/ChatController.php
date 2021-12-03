<?php

namespace app\controllers;

use app\core\controllers\ControllerApi;
use app\core\request\Request;

class ChatController extends ControllerApi
{
    private array $result = [];


    public function actionsMiddle(): array
    {
        return [];
    }

    public function setResult(): array
    {
        return $this->result;
    }

    public function __construct()
    {
    }

    public function index(Request $request)
    {
        $this->result[0] = $_SERVER['WSPHP_SESSION'];
    }

    public function detail()
    {
    }

    public function show()
    {
    }

    public function insert()
    {
    }

    public function update()
    {
    }

    public function delete()
    {
    }
}

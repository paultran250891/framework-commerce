<?php

namespace app\controllers;

// use app\core\controllers\ControllerApi;
use app\core\lib\Test;


class ExampleController
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
        echo "hello world";
    }

    public function index()
    {
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

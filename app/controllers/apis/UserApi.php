<?php

namespace app\controllers\apis;

use app\core\App;
use app\core\controllers\ControllerApi;
use app\core\Request;
use app\models\UserModel;

class UserApi extends ControllerApi
{

    public array $result = [];

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function actionsMiddle(): array
    {


        return [
            'admin' => ['update', 'delete', 'insert'],
            'token' =>  ['update', 'delete', 'insert'],
        ];
    }

    public function setResult(): array
    {
        return  $this->result;
    }

    public function show()
    {
        $this->result[] = $this->userModel->getAll();
    }

    public function index()
    {
        $this->result[] = App::$app->userName();
    }

    public function delete(Request $request)
    {

        $id = $request->getBody()['id'];

        $this->result[] = $this->userModel->_delete(['id' => $id]);
    }
}

<?php

namespace app\controllers\apis;

use app\core\controllers\ControllerApi;
use app\core\Test;
use app\models\ProductModel;

class ExampleApi extends ControllerApi
{

    public $result = []; 
       

    public function __construct()
    {
        $this->producModel = new ProductModel();
    }

    /* abstract */
    function actionsMiddle(): array
    {
        return [ 'detail', 'index'];
    }

    public function setResult(): array
    {
        return $this->result;
    }
    /*   end abstract */

    public function index()
    {   
        $this->result = [$this->producModel->find(['id'=> 1])];
    }

    public function detail()
    {

    }   

   
}
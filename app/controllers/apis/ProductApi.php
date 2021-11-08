<?php

namespace app\controllers\apis;

use app\core\controllers\ControllerApi;
use app\core\Request;
use app\core\Test;
use app\models\ProductModel;

class ProductApi extends ControllerApi
{
    public $result = [];

    public function __construct()
    {
        $this->productModel = new ProductModel();
    }

    /* abstract */
    public function actionsMiddle(): array
    {
        return [
            'admin' => ['update', 'delete', 'insert'],
            'token' =>  ['update', 'delete', 'insert', 'index'],
        ];
    }

    public function setResult(): array
    {
        return $this->result;
    }
    /*   end abstract */

    public function index(Request $request)
    {
        $action = $request->getBody()['product_id'];
        // Test::show($this->productModel->find('all'));
        $this->result[] = $this->productModel->find(['product_id' => $action]);
    }

    public function insert(Request $request)
    {
        $this->result[] = ($this->productModel->validate($request->getBody())) ?
            $this->productModel->_save() : $this->productModel->errors;
        if ($this->productModel->errors) {
            $this->result[1] = 412;
        }
    }

    public function show(Request $request)
    {
        $this->result[] = $this->productModel->find('all');
        // $this->result[0] = ["ok"];
    }

    public function update(Request $request)
    {
        $id = $request->getBody()['id'];
        if ($this->productModel->validate($request->getBody())) {
            $this->productModel->_update(['id' => $id]);
        } else {
            $this->result[] = $this->productModel->errors;
            $this->result[] = 412;
        }
    }

    public function detail(Request $request)
    {


        foreach ($request->getBody() as $attr => $vlaue) {
            if ($attr == 'path') {
                $where = ['path' => $request->getBody()[$attr]];
                $id = $this->productModel->fetch($where, 'url_seo')['id'];
                $this->result[] = $this->productModel->findOne(['url_id' => $id]);
            } elseif ($attr == 'id') {
                $this->result[] = $this->productModel->findOne(['id' => $request->getBody()[$attr]]);
            }
        }
    }

    public function delete(Request $request)
    {
        $id = $request->getBody()['id'];
        $this->productModel->_delete(['id' => $id]);
    }
}

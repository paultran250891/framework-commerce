<?php

namespace app\controllers\apis;

use app\core\controllers\ControllerApi;
use app\core\App;
use app\models\ProductModel;
use app\core\Request;
use app\core\exceptions\NotFoundException;
use app\core\Test;

class CartApi extends ControllerApi
{
    public array $result = [];

    public function __construct(Request $request)
    {
        $this->id = $request->getBody()['id'] ?? false;

        $this->qty = is_numeric($request->getBody()['qty']) ? $request->getBody()['qty'] : 1;
        $this->productModel = new ProductModel();
        $this->session = App::$app->session;
        $this->id = $request->getBody()['id'] ?? false;
        if ($this->session->get('cart')) {
            $this->result = $this->session->get('cart');
        }
    }

    public function actionsMiddle(): array
    {
        return ['token' => [], 'login' => []];
    }

    public function setResult(): array
    {
        foreach ($this->result as $id => $data) {
            if (!is_integer($id) || !$id) {
                throw new NotFoundException('loi tham so id');
            }
            $getModel = $this->productModel->findOne(['id' => $id]);
            if ($getModel) {
                $this->result[$id]['name'] = $getModel['name'];
                $this->result[$id]['price'] = $getModel['price'];
                $this->result[$id]['id'] = $getModel['id'];
                $this->result[$id]['img'] =  $getModel['img'];
            } else {
                throw new NotFoundException('tham so khong co trong san pham');
            }
        }
        $this->session->set('cart', $this->result);
        return [$this->result];
    }

    public function store()
    {
        $this->result[$this->id]['qty'] =  $this->qty ?
            ($this->qty + $this->result[$this->id]['qty']) : (1 + $this->result[$this->id]['qty']);
    }

    public function update()
    {
        $this->result[$this->id]['qty'] = $this->qty;
    }

    public function destroy()
    {
        $this->session->set('cart', false);
        $this->result = [];
    }

    public function delete()
    {
        unset($this->result[$this->id]);
    }

    public function show()
    {
    }
}

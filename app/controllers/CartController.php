<?php

namespace app\controllers;

use app\core\App;
use app\core\Controller;
use app\core\controllers\ControllerApi;
use app\core\database\mongodb\DatabaseMongodb;
use app\core\exceptions\NotFoundException;
use app\core\lib\Test;
use app\core\middlewares\AuthMiddleware;
use app\core\request\Request;
use app\core\session\Session;
use app\models\mongodb\collection\product\Option;
use app\models\mongodb\collection\product\OptionMongodb;
use app\models\mongodb\model\CartModel;
use app\models\ProductModel;

class CartController extends ControllerApi
{
    protected string $id;
    protected int $qty;
    protected array $carts = [];
    protected array $result = [];

    public function actionsMiddle(): array
    {
        return [];
    }

    public function setResult(): array
    {
        return $this->result;
    }

    public function __construct(Request $request)
    {
        $middle = new AuthMiddleware();
        $middle->login(['update', 'store', 'insert']);
        $middle->checkSubmit(['update', 'store', 'insert']);
        $middle->execute();
        // $this->id = $request->getBody()['id'] ?? false;
        // $this->qty = (!empty($request->getBody()['qty']) && is_numeric($request->getBody()['qty']))
        //     ? $request->getBody()['qty'] : false;
        // $this->carts =  App::$app->session->get('cart') ?? [];
    }

    public function insert(Request $request)
    {
        $cart = new CartModel();
        $option = new Option();
        $data = $request->getBody();

        //check cart
        foreach( $data['carts'] as $detail){
            
            $id = DatabaseMongodb::_id( $detail['id']);
           
            $option->filter = ['_id'=> $id];
            if( empty($option->count()) || empty($detail['qty']) ){
                $this->result[0] = 'cart error';
                $this->result[1] = 401;
                return;
                exit();
            };
        }
        if ($cart->validate($data)) {
            $this->result[0] = $cart->insert();
        } else {
            $this->result[0] = $cart->errors;
            $this->result[1] = 412;
        }
    }

    public function count(Request $request)
    {
        $this->result[0] = $this->carts['count'] ?? 0;
    }

    public function update(Request $request)
    {
        if (!$this->qty || !$this->id) {
            throw new NotFoundException('truyen tham so khong dung');
        }
        $this->carts[$this->id]['qty'] =  $this->qty;
        $this->result();
    }

    public function store(Request $request)
    {
        $this->qty = $this->qty ?? 1;
        if (!$this->id) {
            $this->result[0] = "rong";
            $this->result[1] = 404;
            return;
        } elseif (empty($this->carts[$this->id])) {
            $this->carts[$this->id] = ['qty' => $this->qty];
        } else {
            $this->carts[$this->id]['qty'] +=  $this->qty;
        }

        $this->result();
    }

    public function show()
    {
        $this->result();
    }

    public function delete(Request $request)
    {
        unset($this->carts[$this->id]);
        $this->result();
    }

    public function destroy()
    {
        App::$app->session->remove(Session::CART);
    }

    public function result()
    {
        $this->carts['count'] = 0;
        foreach ($this->carts as $id => $cart) {
            if ($id !== 'count') {
                $option = new Option();
                $getProduct = $option->filterCart($id);
                $getProduct['qty'] = $this->carts[$id]['qty'];
                if (!empty($getProduct)) {
                    $this->carts[$id] = $getProduct;
                    $this->carts['count'] += $cart['qty'];
                } else {
                    $this->destroy();
                    throw new NotFoundException('truyen tham so khong dung');
                }
            }
        }
        App::$app->session->set('cart', $this->carts);
        $this->result[0] = $this->carts;
    }
}

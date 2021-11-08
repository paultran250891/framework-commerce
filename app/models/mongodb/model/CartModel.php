<?php

namespace app\models\mongodb\model;

use app\core\App;
use app\core\database\mongodb\DatabaseMongodb;
use app\core\lib\Test;
use app\core\session\Session;
use app\models\mongodb\collection\cart\Detail;
use app\models\mongodb\collection\cart\Order;
use app\models\mongodb\collection\e_commerce\User;
use app\models\mongodb\validate\Validate;
use DateTime;

class CartModel extends Validate
{
    protected string $name;
    protected int $code;
    protected  $create_at;
    protected  $phone;
    protected string $note = '';
    protected int $status = 1;
    protected string $addr;
    protected string $payment;
    protected array $rules = [];

    private  array $dataOrder = [
        'name', 'code', 'note', 'status', 'payment', 'addr', 'create_at', 'phone'
    ];

    public function __construct(array $data = [])
    {
    }

    public function rules(): array
    {
        return [
            'payment' => [
                self::RULE_RIQUIRED => ['Thanh Toán'],
            ],
            'phone' => [
                self::RULE_RIQUIRED => ['Số điện thoại'],
                self::RULE_INT => ['Số điện thoại'],
            ],
            'name' => [
                self::RULE_RIQUIRED => ['Tên'],
            ],
            'addr' => [
                self::RULE_RIQUIRED => ['Địa chỉ'],
            ],

        ];
    }


    public function validate($data): bool
    {
        $this->loadData($data);
        $this->create_at = date("Y-m-d H:i:s");
        $this->code = rand(1000000, 7777777);
        return $this->_validate();
    }

    public function insert()
    {
        $cartOrder = new Order();
        $cartDetail = new Detail();
        $data['email'] =  App::$app->session->get(Session::USER)['email'];
        
        foreach ($this->dataOrder as $attr) {
            $data[$attr] = $this->$attr;
        }

        $_id =  $cartOrder->insert($data);
        foreach($this->carts as $cart){
            $cartDetail->insert([
                'product_id' => DatabaseMongodb::_id($cart['id']),
                'order_id' => $_id,
                'qty'=> $cart['qty']
            ]);
        }
        return 'success';
    }
}

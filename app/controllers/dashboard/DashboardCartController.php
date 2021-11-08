<?php
namespace app\controllers\dashboard;

use app\core\controllers\ControllerApi;
use app\core\database\mongodb\DatabaseMongodb;
use app\core\database\mongodb\filter\cart\CartFilter ;
use app\core\request\Request;
use app\models\mongodb\collection\cart\Detail;
use app\models\mongodb\collection\cart\Order;

class DashboardCartController extends ControllerApi
{
    private array $result = [];

    public function setResult(): array
    {
        return $this->result ;
    }

    public function actionsMiddle(): array
    {
        return [
            'admin' => ['show']
        ];
    }

    public function show(Request $request)
    {
        $req = $request->getBody();
        $action = $req['action'];
        $cart = new Order();



        switch ($action) {
            case 'all':
                $sort = $req['sort'] ?? ['_id' => 1];
                $this->result[0] =$cart->filter = [
                    ['$sort' => $sort],
                    ['$skip' => $req['skip']],
                    ['$limit' => $req['limit']]
                ];
                $this->result[0] = $cart->aggregate();
                break;
            case 'count':
                $this->result[0] = $cart->count();
                break;
            case 'detail':
                $detail = new Detail();
                $detail->filter = CartFilter::detailOfOrder();
                array_push($detail->filter, [
                    '$match' => ['order_id'=> DatabaseMongodb::_id($req['id']) ]
                ]);
                $this->result[0]=$detail->aggregate();

            default:
                # code...
                break;
        }

    }

    public function delete()
    {}


    

}
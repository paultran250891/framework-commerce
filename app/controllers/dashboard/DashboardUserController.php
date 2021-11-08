<?php
namespace app\controllers\dashboard;

use app\core\controllers\ControllerApi;
use app\core\database\mongodb\DatabaseMongodb;
use app\core\request\Request;
use app\models\mongodb\collection\e_commerce\User;

class DashboardUserController extends ControllerApi 
{
    public array $result = [];
    public function __construct()
    {
        
    }

    public function setResult(): array
    {
        return $this->result;
    }

    public function actionsMiddle(): array
    {
        return [
            'admin' => ['show', 'delete']
        ];
    }

    public function show(Request $request)
    {   
        $req = $request->getBody();
        $user = new User();
        $user->filter = [
            ['$sort' => $req['sort']],
            ['$skip' => $req['skip']],
            ['$limit'=> $req['limit']]
        ];
        $this->result[0]['user'] = $user->aggregate();
        $user->filter = [];
        $this->result[0]['count'] =  $user->count();
    }

    public function delete(Request $request)
    {
        $id =  $request->getBody()['id'];
        $user = new User();
        $user->filter = ['_id'=> DatabaseMongodb::_id($id)];
        $this->result[0] = $user->delete();
        
    }

    
}
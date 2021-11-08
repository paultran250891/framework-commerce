<?php

namespace app\models\mongodb\model;

use app\core\App;
use app\core\lib\Test;
use app\models\mongodb\collection\e_commerce\User;
use app\models\mongodb\validate\Validate;

class LoginModel extends Validate
{
    protected string $email;
    protected string $pass;
    protected string $repass;
    protected int $status;
    protected string $addr;
    protected string $img = '/img/avarta/avarta.jpg';
    protected string $name;
    protected array $rules = [];

    public function __construct(array $data = [])
    {
    }

    public function rules(): array
    {
        return [
            'email' => [
                self::RULE_RIQUIRED => [],
                self::RULE_MATCHDB => [User::class, 'Địa chỉ Email'],
                self::RULE_EMAIL => [],
            ],
            'pass' => [
                self::RULE_RIQUIRED => ['Mật khẩu'],

            ]

        ];
    }

    public function validate($data): bool
    {
        $this->loadData($data);
        if ($this->_validate()) {
            $user = new User();
            $user->filter = [
                'email' => $this->email,
                'pass' => $this->pass,
            ];
            $data =  $user->findOne();
            if (!empty($data)) {
                return true;
            }
            $this->addError('pass', 'Mật khẩu không đúng');
        }
        return false;
    }

    public function active($email) : bool
    {
        $user = new User();
        $user->filter= [
            'email' => $email
        ];
        if($user->findOne()['active'] == 1){
            App::$app->session->set('user', $user->findOne());
            return true;
        }
        
        return false;
    }

    public function login()
    {
        
    }

}

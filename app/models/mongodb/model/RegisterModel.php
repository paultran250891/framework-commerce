<?php

namespace app\models\mongodb\model;

use app\core\App;
use app\core\lib\Test;
use app\models\mongodb\collection\e_commerce\User;
use app\models\mongodb\validate\Validate;

class RegisterModel extends Validate
{
    protected string $email;
    protected string $pass;
    protected string $repass;
    protected int $status =  1;
    protected string $addr = '';
    protected string $name;
    protected array $rules = [];
    protected string $img = '/img/avarta/avarta.jpg';
    protected int $code;
    public string $hashCode;

    public function rules(): array
    {
        return [
            'email' => [
                self::RULE_RIQUIRED => [],
                self::RULE_UNIQUE => [User::class, 'Địa chỉ Email'],
                self::RULE_EMAIL => [],
            ],
            'pass' => [
                self::RULE_RIQUIRED => ['Mật khẩu'],
                self::RULE_MAX => [12, 'Mật khẩu'],
                self::RULE_MIN => [6, 'Mật khẩu'],
            ],
            'repass' => [
                self::RULE_RIQUIRED => ['Mật khẩu'],
                self::RULE_MATCH => ['pass', 'Mật khẩu']
            ]
        ];
    }

    public function data(): array
    {
        return ['email', 'pass', 'status', 'name', 'code', 'img'];
    }

    public function validate($data): bool
    {
        $this->loadData($data);
        return $this->_validate();
    }

    public function insert($data)
    {

        $data = [];
        $user = new User();
        $this->name =  $this->name ?? ucwords(explode('@', $this->email)[0]);
        $this->code = rand(1000,9999);
        $this->hashCode = password_hash($this->code, PASSWORD_DEFAULT) ;
        foreach ($this->data() as $attr) {
            $data[$attr] = $this->{$attr};
        }
        // Test::show($data);
        $user->insert($data);
        return 'success';
        
    }
}

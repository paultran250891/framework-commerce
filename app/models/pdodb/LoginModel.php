<?php

namespace app\models\pdodb;

use app\core\App;

use app\core\pdodb\database\DbModel;

class LoginModel extends DbModel
{

    public string $name = '';
    public string $email = '';
    public string $pass = '';
    public string $repass = '';
    public string $phone = '';
    public $create_at = '';
    public string $img = '';


    public function rules(): array
    {
        return [
            'email' => [self::RULE_RIQUIRED],
            'pass' => [self::RULE_RIQUIRED, [self::RULE_MIN, 'min' => 6], [self::RULE_MAX, 'max' => 24]],
        ];
    }

    public function labels(): array
    {
        return [
            'email' => 'Email',
            'pass' => 'Pass'
        ];
    }

    public function tableName(): string
    {
        return 'users';
    }

    public function attributes(): array
    {
        return [
            'email', 'pass', 'img', 'name'
        ];
    }

    public function primaryKey(): string
    {
        return 'id';
    }

    public function login()
    {
        $user = parent::findOne(['email' => $this->email]);
        if (!$user) {
            $this->addError('email', 'email nay` khong ton` tai. !!!');
            return false;
        }
        // Test::show($user->pass);

        if (!password_verify($this->pass, $user['pass'])) {
            $this->addError('pass', 'mat khau nay` khong dung');
            return false;
        }
        return App::$app->login($user);
    }

    public function loginGG()
    {
        $this->create_at = date('Y/m/d H:i:s');
        $user = parent::findOne(['email' => $this->email]);
        if (!$user) {
            $this->pass = "khongthelaymatkhaunay";
            $user = parent::save();
        }
        $user = parent::findOne(['email' => $this->email]);
        return App::$app->login($user);
    }

    public function _delete($where)
    {
        if (parent::delete($where)) {
            return 'success';
        };
        return 'fail';
    }
}

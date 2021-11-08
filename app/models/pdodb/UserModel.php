<?php

namespace app\models\pdodb;

use app\core\model\Model;
use app\core\pdodb\database\DbModel;
use GrahamCampbell\ResultType\Success;

class UserModel extends DbModel
{
    public const STATUS_INACTIVE = 0;

    public string $name = '';
    public string $email = '';
    public int $status;
    public string $pass = '';
    public string $repass = '';
    public string $img = '/img/yord.jpg';
    public $create_at  = '';
    public $note = '';



    public function __construct()
    {
        $this->create_at = date('Y/m/d H:i:s');
    }

    public function attributes(): array
    {
        return [
            'email', 'name', 'status', 'pass', 'create_at', 'img'
        ];
    }

    public function primaryKey(): string
    {
        return 'id';
    }

    public function tableName(): string
    {
        return 'users';
    }

    public function rules(): array
    {

        return [
            'email' => [self::RULE_RIQUIRED, self::RULE_EMAIL, [self::RULE_UNIQUE, 'class' => self::class]],
            'pass' => [self::RULE_RIQUIRED, [self::RULE_MIN, 'min' => 6], [self::RULE_MAX, 'max' => 24]],
            'repass' => [self::RULE_RIQUIRED, [self::RULE_MATCH, 'match' => 'pass']]
        ];
    }

    public function labels(): array
    {
        return [
            'email' => 'Email',
            'pass' => "Mat khau",
            'repass' => "Mat khau"
        ];
    }

    public function save()
    {
        $this->create_at = date('Y/m/d H:i:s');

        $this->name = str_replace('@gmail.com', '', $this->email);
        $this->status = self::STATUS_INACTIVE;
        $this->pass = password_hash($this->pass, PASSWORD_DEFAULT);
        // return $this;
        return parent::save();
    }

    public function _delete($where)
    {
        if (parent::delete($where)) {
            return 'success';
        };
        return 'fail';
    }
}

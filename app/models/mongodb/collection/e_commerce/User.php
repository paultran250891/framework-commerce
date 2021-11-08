<?php

namespace app\models\mongodb\collection\e_commerce;

use app\core\database\mongodb\MongoDb;
use app\models\mongodb\validate\Validate;
use DateTime;

class User extends MongoDb
{
    protected string  $email;
    protected string $pass;
    protected int $status;
    protected $name;
    public array $filter = [];
    public array $option = [];

    public function __construct(array $data = [])
    {
    }

    protected function collection(): string
    {
        return 'users';
    }
    public function filter(): array
    {
        return $this->filter;
    }

    public function option(): array
    {
        return $this->option;
    }
}

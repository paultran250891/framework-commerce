<?php

declare(strict_types=1);

namespace app\models\mongodb\collection\e_commerce;

use app\core\database\mongodb\MongoDb;

class Url extends MongoDb
{

    public function collection(): string
    {
        return 'urls';
    }

    public array $filter = [];
    public array $option = [];

    public function filter(): array
    {
        return $this->filter;
    }

    public function option(): array
    {
        return $this->option;
    }
}

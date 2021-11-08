<?php

declare(strict_types=1);

namespace app\models\mongodb\collection\cart;

use app\core\database\mongodb\MongoDb;

class Detail extends MongoDb
{

    public function collection(): string
    {
        return 'cart_details';
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

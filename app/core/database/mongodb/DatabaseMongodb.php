<?php

namespace app\core\database\mongodb;

use app\core\App;
use app\core\lib\Test;

class DatabaseMongodb
{
    private static \MongoDB\Client $client;
    private static string $dbName;

    public function __construct($mongo)
    {
        self::$client =  new \MongoDB\Client($mongo['connect']);
        self::$dbName = $mongo['dbName'];
    }

    public function connectDb()
    {
        return self::$client->{self::$dbName};
    }

    public static function _id($_id)
    {
        return new \MongoDB\BSON\ObjectID($_id);
    }
}

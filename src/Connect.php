<?php
/*
 * Created 13.12.2019 20:03
 */

namespace IT\Technology\ORM;

use PDO;

/**
 * Class Connect
 * @package IT\Technology\ORM
 * @author Alexandr Pokatskiy
 * @copyright ITTechnology
 */
class Connect
{
    /**
     * Объект подключения к базе данных
     * @var null
     */
    private static $Instance = null;

    /**
     * Параметры подключения
     * @var array
     */
    private static $dbData = [];

    /**
     * Подключить к базе данных
     * @return PDO|null
     */
    public static function getInstance()
    {
        if(is_null(self::$Instance))
        {
            self::$Instance = new PDO(
                "mysql:host=".self::$dbData["host"].";port=".self::$dbData["port"].";dbname=".self::$dbData["database"],
                self::$dbData["user"],
                self::$dbData["password"],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES ".self::$dbData["charset"]
                ]
            );
        }

        return self::$Instance;
    }

    /**
     * Передать параметры подключения
     * @param array $data
     */
    public static function create(array $data)
    {
        self::$dbData = $data;
    }


    private function __construct(){}
    private function __clone(){}
    private function __wakeup(){}
}
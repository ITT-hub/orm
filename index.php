<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/vendor/autoload.php";
require_once __DIR__."/User.php";

use ITTech\ORM\Connect;

$data = [
    "host"     => "localhost",
    "port"     => 3306,
    "database" => "shop",
    "user"     => "root",
    "password" => "",
    "charset"  => "utf8"
];

Connect::create($data);

var_dump(User::find(1));
echo "<br>";
var_dump(User::where("enable", 1)->get());
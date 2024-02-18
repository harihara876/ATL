<?php

require_once("./init/init.php");

use Plat4m\App\DB;
use Plat4m\Utilities\Logger;

echo $_SERVER["SERVER_NAME"];
echo "<br>";

$db = (new DB)->getConn();
var_dump($db);

Logger::infoMsg("Simple info message");
Logger::debugMsg(["a" => "one", "b" => "Two", "c" => 23]);
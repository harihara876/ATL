<?php

require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Core\API\OrderPrice;
use Plat4m\Utilities\Logger;
use Plat4m\Utilities\Response;

try {
    Middleware::verifyAuth();

    Logger::httpMsg();
    $db = (new DB)->getConn();
    $users = (new OrderPrice($db))->getAll();
    $formattedUsers = [];

    if ($users) {
        foreach ($users as $user) {
            $formattedUsers[] = [
                "id"                => $user["id"],
                "product_id"        => $user["product_id"],
                "storeadmin_id"     => $user["storeadmin_id"],
                "type_app_admin"    => $user["type_app_admin"],
                "sellprice"         => $user["sellprice"]
            ];
        }
    } else {
        throw new Exception("No users found", 404);
    }

    Logger::infoMsg(sprintf("Returned users count: %d", count($formattedUsers)));
    Response::statusCode(200)::body([
        "error" => FALSE,
        "users" => $formattedUsers
    ])::json();
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body(["error" => $ex->getMessage()])::json();
}

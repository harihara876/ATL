<?php

require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Core\API\DeviceUser;
use Plat4m\Utilities\Logger;
use Plat4m\Utilities\Response;

try {
    Middleware::verifyAuth();

    Logger::httpMsg();
    $db = (new DB)->getConn();
    $users = (new DeviceUser($db))->getAll();
    $formattedUsers = [];

    if ($users) {
        foreach ($users as $user) {
            $formattedUsers[] = [
                "id"                => $user["id"],
                "first_name"        => $user["first_name"],
                "last_name"         => $user["last_name"],
                "email"             => $user["email"],
                "storeadmin_id"     => $user["storeadmin_id"],
                "type_app_admin"    => $user["type_app_admin"]
            ];
        }
    } else {
        throw new Exception("No device users found", 404);
    }

    Logger::infoMsg(sprintf("Returned device users: %d", count($formattedUsers)));
    Response::statusCode(200)::body([
        "error" => FALSE,
        "users" => $formattedUsers
    ])::json();
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body(["error" => $ex->getMessage()])::json();
}

<?php

require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Core\API\DeviceUser;
use Plat4m\Utilities\Helper;
use Plat4m\Utilities\Logger;
use Plat4m\Utilities\Request;
use Plat4m\Utilities\Response;

try {
    Middleware::verifyAuth();

    Logger::httpMsg();
    $payload = Request::payload();
    $input = [
        "id"            => Helper::arrVal($payload, "id"),
        "firstName"     => Helper::arrVal($payload, "first_name"),
        "lastName"      => Helper::arrVal($payload, "last_name"),
        "email"         => Helper::arrVal($payload, "email"),
        "password"      => Helper::arrVal($payload, "password")
    ];

    $db = (new DB)->getConn();
    $affectedRows = (new DeviceUser($db))->updateDeviceUser(
        $input["id"],
        $input["firstName"],
        $input["lastName"],
        $input["email"],
        $input["password"]
    );
    Logger::infoMsg(sprintf("Update device user count: %d", $affectedRows));

    if (!$affectedRows) {
        throw new Exception("Unable to update", 500);
    }

    Response::statusCode(200)::body([
        "error"     => FALSE,
        "message"   => "Successfully updated",
    ])::json();
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body([
        "error"     => TRUE,
        "message"   => $ex->getMessage()
    ])::json();
}

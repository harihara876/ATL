<?php

require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Core\API\DeviceUser;
use Plat4m\Utilities\Helper;
use Plat4m\Utilities\Logger;
use Plat4m\Utilities\Request;
use Plat4m\Utilities\Response;
use Plat4m\Utilities\Validator;

try {
    Middleware::verifyAuth();
    Logger::httpMsg();

    $payload = Request::payload();
    $input = [
        "email"         => Helper::arrVal($payload, "email"),           // Email.
        "password"      => Helper::arrVal($payload, "password"),        // Password.
        "firstName"     => Helper::arrVal($payload, "first_name"),      // Last name.
        "lastName"      => Helper::arrVal($payload, "last_name"),       // First name.
        "storeAdminID"  => Helper::arrVal($payload, "store_admin_id")   // Store admin ID.
    ];

    // Validate input.
    $v = new Validator();
    $v->name("Email")->str($input["email"])->reqStr()->strEmail();
    $v->name("Password")->str($input["password"])->reqStr()->minStr(4);
    $v->name("First name")->str($input["firstName"])->reqStr();
    $v->name("Last name")->str($input["lastName"])->reqStr();
    $v->name("Store admin ID")->nInt($input["storeAdminID"]);

    if ($v->anyErrors()) {
        throw new Exception($v->errStr(), HTTP_STATUS_BAD_REQUEST);
    }

    $db = (new DB)->getConn();

    // Check if email exists in device users.
    $deviceUser = (new DeviceUser($db))->getInfoByEmail($input['email']);

    if (isset($deviceUser["email"])) {
        throw new Exception("Email already exists", HTTP_STATUS_BAD_REQUEST);
    }

    // Check if email exists in admin users.
    $adminUser = (new DeviceUser($db))->getAdminInfoByEmail($input['email']);

    if (isset($adminUser["email"])) {
        throw new Exception("Email already exists", HTTP_STATUS_BAD_REQUEST);
    }

    // Create device user.
    $deviceUserId = (new DeviceUser($db))->insertDeviceUser(
        $input['firstName'],
        $input['lastName'],
        $input['email'],
        $input['password'],
        $input['storeAdminID']
    );

    if ($deviceUserId) {
        Response::statusCode(201)::body([
            "id"                => $deviceUserId,
            "first_name"        => $input["firstName"],
            "last_name"         => $input["lastName"],
            "email"             => $input["email"],
            "store_admin_id"    => $input['storeAdminID']
        ])::json(JSON_NUMERIC_CHECK);
    } else {
        throw new Exception("Unable to create", HTTP_STATUS_INTERNAL_SERVER_ERROR);
    }
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body([
        "error" => $ex->getMessage()
    ])::json();
}

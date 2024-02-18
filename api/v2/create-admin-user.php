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
    Logger::httpMsg();

    $payload = Request::payload();
    $input = [
        "first_name"        => Helper::arrVal($payload, "first_name"),
        "last_name"         => Helper::arrVal($payload, "last_name"),
        "email"             => Helper::arrVal($payload, "email"),
        "password"          => Helper::arrVal($payload, "password"),
        "mobile_number"     => Helper::arrVal($payload, "mobile_number"),
        "store_type"        => Helper::arrVal($payload, "store_type"),
        "store_name"        => Helper::arrVal($payload, "store_name"),
        "store_address"     => Helper::arrVal($payload, "store_address"),
        "store_city"        => Helper::arrVal($payload, "store_city"),
        "store_zip"         => Helper::arrVal($payload, "store_zip"),
        "store_country"     => Helper::arrVal($payload, "store_country"),
    ];

    // Validate input.
    $v = new Validator();
    $v->name("First name")->str($input["first_name"])->reqStr();
    $v->name("Last name")->str($input["last_name"])->reqStr();
    $v->name("Email")->str($input["email"])->reqStr()->strEmail();
    $v->name("Password")->str($input["password"])->reqStr()->minStr(4);
    $v->name("Mobile number")->str($input["mobile_number"])->reqStr();
    $v->name("Store type")->str($input["store_type"])->reqStr();
    $v->name("Store name")->str($input["store_name"])->reqStr();
    $v->name("Store address")->str($input["store_address"])->reqStr();
    $v->name("Store city")->str($input["store_city"])->reqStr();
    $v->name("Store ZIP")->str($input["store_zip"])->reqStr();
    $v->name("Store country")->str($input["store_country"])->reqStr();

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

    // Create admin user.
    $fullName = $input['first_name'] . " " . $input["last_name"];
    $adminUserId = (new DeviceUser($db))->insertAdminUser(
        $fullName,
        $input['email'],
        $input['password']
    );

    if ($adminUserId) {
        Response::statusCode(201)::body([
            "id"            => (int) $adminUserId,
            "first_name"    => (string) $input["first_name"],
            "last_name"     => (string) $input["last_name"],
            "email"         => (string) $input["email"],
            "password"      => (string) $input["password"],
            "mobile_number" => (string) $input["mobile_number"],
            "store_type"    => (string) $input["store_type"],
            "store_name"    => (string) $input["store_name"],
            "store_address" => (string) $input["store_address"],
            "store_city"    => (string) $input["store_city"],
            "store_zip"     => (string) $input["store_zip"],
            "store_country" => (string) $input["store_country"],
        ])::json();
    } else {
        throw new Exception("Unable to create", HTTP_STATUS_INTERNAL_SERVER_ERROR);
    }
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body([
        "error" => $ex->getMessage()
    ])::json();
}

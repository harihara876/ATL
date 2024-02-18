<?php

require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Core\API\Admin;
use Plat4m\Utilities\Helper;
use Plat4m\Utilities\Logger;
use Plat4m\Utilities\Mailer;
use Plat4m\Utilities\OTP;
use Plat4m\Utilities\Request;
use Plat4m\Utilities\Response;

try {
    // Middleware::verifyAuth();

    // Get input params.
    $payload = Request::payload();
    Logger::httpMsg($payload);
    $input = [
        "email" => filter_var(Helper::arrVal($payload, "email"), FILTER_VALIDATE_EMAIL),
    ];

    if (empty($input["email"])) {
        throw new Exception("Invalid email", 400);
    }

    $db = (new DB)->getConn();
    $adminHandler = new Admin($db);

    // Get admin details.
    $admin = $adminHandler->getInfoByEmail($input["email"]);

    if (!$admin) {
        throw new Exception("Email not found", 400);
    }

    // Save email and OTP in DB.
    // $otp = OTP::generate(4); // TODO: For testing hardcoded OTP.
    $otp = "3456";
    $inserted = $adminHandler->storeOTP($input["email"], $otp, date("Y-m-d H:i:s"));

    if (!$inserted) {
        throw new Exception("Failed to create OTP", 500);
    }

    // Send email.
    $subject = "Reset Your Password";
    $body = "Hi <b>{$admin->name}</b><br><br>";
    $body .= "We've received a request to reset your password.<br>";
    $body .= "You can reset your password using this OTP <b>{$otp}</b><br><br>";
    $body .= "Thanks<br>";
    $body .= "Plat4m Inc.";
    $result = (new Mailer)->send($input["email"], $admin->name, $subject, $body);

    if ($result == 1) {
        Response::statusCode(200)::body([
            "err" => "",
            "msg" => "OTP sent successfully to your registered email"
        ])::json();
    } else {
        throw new Exception("Failed to send OTP", 500);
    }
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body([
        "err" => $ex->getMessage(),
        "msg" => ""
    ])::json();
}

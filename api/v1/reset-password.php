<?php

require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Core\API\Admin;
use Plat4m\Utilities\Helper;
use Plat4m\Utilities\Logger;
use Plat4m\Utilities\Request;
use Plat4m\Utilities\Response;

try {
    // Middleware::verifyAuth();

    // Get input params.
    $payload = Request::payload();
    Logger::httpMsg();
    $input = [
        "email"     => filter_var(Helper::arrVal($payload, "email"), FILTER_VALIDATE_EMAIL),
        "otp"       => Helper::arrVal($payload, "otp"),
        "password"  => Helper::arrVal($payload, "password"),
    ];

    if (empty($input["email"])) {
        throw new Exception("Invalid email", 400);
    }

    if (empty($input["otp"])) {
        throw new Exception("Invalid OTP", 400);
    }

    if (empty($input["password"])) {
        throw new Exception("Invalid password", 400);
    }

    $db = (new DB)->getConn();
    $adminHandler = new Admin($db);

    // Get admin details.
    $admin = $adminHandler->getInfoByEmail($input["email"]);

    if (!$admin) {
        throw new Exception("Email not found", 400);
    }

    // Get OTP info and verify.
    $otpInfo = $adminHandler->getOTPInfo($input["email"], $input["otp"]);
    $valid = $adminHandler->verifyOTP($otpInfo);

    if (!$valid) {
        throw new Exception("OTP invalid or expired", 400);
    }

    // If old and new passwords are same, return success message.
    if ($admin->password == $input["password"]) {
        $adminHandler->deleteOTPByID($otpInfo->id);
        Response::statusCode(200)::body([
            "err" => "",
            "msg" => "Password updated successfully"
        ])::json();
    }

    // Update password.
    $updated = $adminHandler->updatePassword($admin->email, $input["password"]);

    if ($updated) {
        $adminHandler->deleteOTPByID($otpInfo->id);
        Response::statusCode(200)::body([
            "err" => "",
            "msg" => "Password updated successfully"
        ])::json();
    } else {
        throw new Exception("Failed to update password", 500);
    }
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body([
        "err" => $ex->getMessage(),
        "msg" => ""
    ])::json();
}

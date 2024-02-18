<?php

use Plat4m\App\Auth;
use Plat4m\App\Claims;
use Plat4m\App\DB;
use Plat4m\Core\API\DeviceUser;
use Plat4m\Utilities\Logger;
use Plat4m\Utilities\Request;
use Plat4m\Utilities\Response;

require_once("../../init/init.php");

try {
    Logger::httpMsg();
    $refreshToken = Request::getAuthBearerToken();

    if (!$refreshToken) {
        throw new Exception("Invalid refresh token", HTTP_STATUS_UNAUTHORIZED);
    }

    $verified = Auth::verifyJWT($refreshToken, JWT_SECRET, JWT_ISSUER,
        JWT_SIGN_ALGO, JWT_TYPE_REFRESH, $payload);

    if (!$verified || $verified != HTTP_STATUS_OK) {
        throw new Exception("Invalid refresh token", HTTP_STATUS_UNAUTHORIZED);
    }

    $db = (new DB)->getConn();
    $adminUser = (new DeviceUser($db))->getAdminInfoByEmail($payload->email);

    if (isset($adminUser["email"])) {
        list($accessToken, $refreshToken) = Auth::generateTokensPair(
            Claims::adminClaims($adminUser)
        );
        Response::statusCode(200)::body([
            "access_token"     => $accessToken,
            "refresh_token"    => $refreshToken
        ])::json();
    } else {
        $deviceUser = (new DeviceUser($db))->getInfoByEmail($payload->email);

        if (isset($deviceUser["email"])) {
            list($accessToken, $refreshToken) = Auth::generateTokensPair(
                Claims::deviceUserClaims($deviceUser)
            );
            Response::statusCode(200)::body([
                "access_token"     => $accessToken,
                "refresh_token"    => $refreshToken
            ])::json();
        }
    }

    throw new Exception("User not found", 404);
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body([
        "error"     => TRUE,
        "message"   => $ex->getMessage()
    ])::json();
}

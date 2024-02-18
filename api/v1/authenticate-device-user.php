<?php

require_once("../../init/init.php");

use Plat4m\App\Auth;
use Plat4m\App\Claims;
use Plat4m\App\DB;
use Plat4m\Core\API\Admin;
use Plat4m\Core\API\DeviceUser;
use Plat4m\Utilities\Helper;
use Plat4m\Utilities\Logger;
use Plat4m\Utilities\Request;
use Plat4m\Utilities\Response;
use Plat4m\Utilities\Validator;

$email      = Request::postVal("email");
$password   = Request::postVal("password");

try {
    Logger::httpMsg(getallheaders());

    // Validate input.
    $v = new Validator();
    $v->name("Email")->str($email)->reqStr()->strEmail();
    $v->name("Password")->str($password)->reqStr();

    if ($v->anyErrors()) {
        throw new Exception($v->errStr(), HTTP_STATUS_BAD_REQUEST);
    }

    $db = (new DB)->getConn();
    $adminUser = (new DeviceUser($db))->getAdminInfoByEmail($email);

    if (isset($adminUser["email"])) {
        if (password_verify($password, $adminUser["pwhash"])) {
            list($accessToken, $refreshToken) = Auth::generateTokensPair(
                Claims::adminClaims($adminUser)
            );
            $paytmCredentials = empty($adminUser["paytm_credentials"])
                ? NULL
                : json_decode(base64_decode($adminUser["paytm_credentials"]));
            $response = [
                "id"                    => $adminUser["admin_id"],
                "name"                  => $adminUser["name"],
                "email"                 => $adminUser["email"],
                "storeadmin_id"         => $adminUser["admin_id"],
                "type"                  => $adminUser["type_appstatus"],
                "error"                 => FALSE,
                "authenticated"         => TRUE,
                "access_token"          => $accessToken,
                "refresh_token"         => $refreshToken,
                "paytm_credentials"     => $paytmCredentials,
                "currency_symbol"       => $adminUser["currency_symbol"],
            ];
        } else {
            $response = [
                "error"             => TRUE,
                "authenticated"     => FALSE,
            ];
        }
    } else {
        $deviceUser = (new DeviceUser($db))->getInfoByEmail($email);

        if ($deviceUser && password_verify($password, $deviceUser["password"])) {
            list($accessToken, $refreshToken) = Auth::generateTokensPair(
                Claims::deviceUserClaims($deviceUser)
            );
            $adminHadler = new Admin($db);
            $currencyInfo = $adminHadler->getCurrency($deviceUser["storeadmin_id"]);
            $paytmCredentials = $adminHadler->getPaytmCredentials($deviceUser["storeadmin_id"]);
            $paytmCredentials = empty($paytmCredentials)
                ? NULL
                : json_decode(base64_decode($paytmCredentials));
            $response = [
                "id"                    => $deviceUser["id"],
                "name"                  => $deviceUser["first_name"],
                "email"                 => $deviceUser["email"],
                "storeadmin_id"         => $deviceUser["storeadmin_id"],
                "type"                  => "DeviceUser",
                "error"                 => FALSE,
                "authenticated"         => TRUE,
                "access_token"          => $accessToken,
                "refresh_token"         => $refreshToken,
                "paytm_credentials"     => $paytmCredentials,
                "currency_symbol"       => Helper::arrVal($currencyInfo, "currency_symbol", DEFAULT_CURRENCY_SYMBOL),
            ];
        } else {
            $response = [
                "error"             => TRUE,
                "authenticated"     => FALSE,
            ];
        }
    }

    Response::statusCode(200)::body($response)::json();
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body([
        "error"     => TRUE,
        "message"   => $ex->getMessage()
    ])::json();
}

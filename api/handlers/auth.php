<?php

use Plat4mAPI\App\Auth;
use Plat4mAPI\App\Claims;
use Plat4mAPI\Model\Admin;
use Plat4mAPI\Model\AdminLogin;
use Plat4mAPI\Model\AdminOTP;
use Plat4mAPI\Model\CashierOTP;
use Plat4mAPI\Model\Cashier;
use Plat4mAPI\Model\CashierLogin;
use Plat4mAPI\Util\Logger;
use Plat4mAPI\Util\Mailer;
use Plat4mAPI\Util\Validator;

/**
 * Verifies if admin can login.
 * @param object $ctx Context.
 * @param array $admin Admin info.
 * @return bool
 */
function verifyAdminLogin($ctx, $admin)
{
    // Fetch instance IDs.
    $adminLoginModel = new AdminLogin;
    $instanceIDs = $adminLoginModel->loggedInInstanceIDs(
        $ctx,
        $admin["id"],
        $admin["registered_app"]
    );

    if (in_array($ctx->clientApp->instanceID, $instanceIDs)) {
        return TRUE;
    }

    if (count($instanceIDs) < $admin["allowed_logins"]) {
        $id = $adminLoginModel->create(
            $ctx,
            $admin["id"],
            $admin["registered_app"]
        );
        return !empty($id);
    }

    sendErrJSON(400, ERR_MAX_LOGINS_REACHED);
}

/**
 * Authenticates Admin.
 * @param object $ctx Context.
 * @param string $email Email.
 * @param string $password Password.
 * @return array
 */
function authenticateAdmin($ctx, $email, $password)
{
    // Fetch Admin info and verify password.
    $adminModel = new Admin;
    $admin = '';
    if ($ctx->clientApp->name === "OBCR") {
        $admin = $adminModel->getOBCRUserInfo($ctx, $email);
    } else {
        $admin = $adminModel->getInfoByEmail($ctx, $email, $ctx->clientApp->name);
    }
    $authenticated = $admin && pwVerify($password, $admin["pwhash"]);
    if (!$authenticated) {
        return NULL;
    }

    verifyAdminLogin($ctx, $admin);

    // Generate JWTs and populate Paytm credentials.
    list($accessToken, $refreshToken) = Auth::generateTokensPair(
        Claims::adminClaims($admin)
    );
    $paytmCredentials = empty($admin["paytm_credentials"])
        ? NULL
        : json_decode(base64_decode($admin["paytm_credentials"]));

    return [
        "id"                    => $admin["id"],
        "first_name"            => $admin["first_name"],
        "last_name"             => $admin["last_name"],
        "email"                 => $admin["email"],
        "mobile_number"         => $admin["mobile_number"],
        "admin_id"              => $admin["id"],
        "role"                  => $admin["role"],
        "paytm_credentials"     => $paytmCredentials,
        "currency"              => $admin["currency"],
        "currency_symbol"       => $admin["currency_symbol"],
        "access_token"          => $accessToken,
        "refresh_token"         => $refreshToken,
        "tax"                   => $admin["tax"],
        "store_name"            => $admin["store_name"],
        "street_address"        => $admin["street_address"],
        "store_city"            => $admin["store_city"],
        "store_zip"             => $admin["store_zip"],
        "store_country"         => $admin["store_country"],
        "store_state"           => $admin["store_state"],
    ];
}

/**
 * Verifies if cashier can login.
 * @param object $ctx Context.
 * @param array $cashier Cashier info.
 * @return bool
 */
function verifyCashierLogin($ctx, $cashier)
{
    // Fetch instance IDs.
    $cashierLoginModel = new CashierLogin;
    $instanceIDs = $cashierLoginModel->loggedInInstanceIDs(
        $ctx,
        $cashier["id"],
        $cashier["registered_app"]
    );

    if (in_array($ctx->clientApp->instanceID, $instanceIDs)) {
        return TRUE;
    }

    if (count($instanceIDs) < $cashier["allowed_logins"]) {
        $id = $cashierLoginModel->create(
            $ctx,
            $cashier["id"],
            $cashier["registered_app"]
        );
        return !empty($id);
    }

    sendErrJSON(400, ERR_MAX_LOGINS_REACHED);
}

/**
 * Authenticates Cashier.
 * @param object $ctx Context.
 * @param string $email Email.
 * @param string $password Password.
 * @return array
 */
function authenticateCashier($ctx, $email, $password)
{
    $cashierModel = new Cashier;
    $cashier = $cashierModel->getInfoByEmail($ctx, $email, $ctx->clientApp->name);
    $authenticated = $cashier && pwVerify($password, $cashier["password"]);
    if (!$authenticated) {
        return NULL;
    }

    verifyCashierLogin($ctx, $cashier);

    // Generate JWTs and populate Paytm credentials.
    list($accessToken, $refreshToken) = Auth::generateTokensPair(
        Claims::cashierClaims($cashier)
    );

    $adminModel = new Admin;
    $admin = $adminModel->getInfoByID($ctx, $cashier["storeadmin_id"]);
    $currency = empty($admin["currency"])
        ? DEFAULT_CURRENCY
        : $admin["currency"];
    $currencySymbol = empty($admin["currency_symbol"])
        ? DEFAULT_CURRENCY_SYMBOL
        : $admin["currency_symbol"];
    $paytmCredentials = empty($admin["paytm_credentials"])
        ? NULL
        : json_decode(base64_decode($admin["paytm_credentials"]));
    $tax = $admin['tax'];

    return [
        "id"                    => $cashier["id"],
        "first_name"            => $cashier["first_name"],
        "last_name"             => $cashier["last_name"],
        "email"                 => $cashier["email"],
        "mobile_number"         => $admin["mobile_number"],
        "admin_id"              => $cashier["storeadmin_id"],
        "role"                  => USER_CASHIER,
        "paytm_credentials"     => $paytmCredentials,
        "currency"              => $currency,
        "currency_symbol"       => $currencySymbol,
        "tax"                   => $tax,
        "access_token"          => $accessToken,
        "refresh_token"         => $refreshToken,
    ];
}

/**
 * login handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */

function loginV1($ctx, $args)
{
    $payload = payload();
    $input = [
        "email"     => arrVal($payload, "email"),
        "password"  => arrVal($payload, "password"),
    ];

    $v = new Validator();
    $v->name("Email")->str($input["email"])->reqStr()->strEmail();
    $v->name("Password")->str($input["password"])->reqStr();

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    // Check Admin. If not found, check Cashier.
    $response = authenticateAdmin($ctx, $input["email"], $input["password"]);
    $response = $response ?? authenticateCashier($ctx, $input["email"], $input["password"]);

    if (!$response) {
        sendErrJSON(401, ERR_UNAUTHORIZED);
    }

    sendJSON(200, $response);
}

/**
 * Logout handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function logoutV1($ctx, $args)
{
    if ($ctx->tokenData->type == USER_ADMIN) {
        (new AdminLogin)->delete(
            $ctx,
            $ctx->tokenData->id,
            $ctx->tokenData->registered_app,
        );
    } elseif ($ctx->tokenData->type == USER_CASHIER) {
        (new CashierLogin)->delete(
            $ctx,
            $ctx->tokenData->id,
            $ctx->tokenData->registered_app,
        );
    }

    sendEmpty(204);
}

/**
 * Refresh JWTs handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function refreshTokensV1($ctx, $args)
{
    $refreshToken = getAuthBearerToken();
    if (!$refreshToken) {
        sendErrJSON(401, ERR_UNAUTHORIZED, "Invalid refresh token");
    }

    // Verify refresh token.
    $verified = Auth::verifyJWT(
        $refreshToken,
        JWT_SECRET,
        JWT_ISSUER,
        JWT_SIGN_ALGO,
        JWT_TYPE_REFRESH,
        $claims
    );

    if (!$verified || $verified != 200) {
        sendErrJSON(401, ERR_UNAUTHORIZED, "Invalid refresh token");
    }

    // Generate new pair of tokens.
    $admin = (new Admin)->getInfoByEmail($ctx, $claims->email, $claims->registered_app);

    if ($admin) {
        list($accessToken, $refreshToken) = Auth::generateTokensPair(
            Claims::adminClaims($admin)
        );
        sendJSON(200, [
            "access_token"     => $accessToken,
            "refresh_token"    => $refreshToken
        ]);
    } else {
        $cashier = (new Cashier)->getInfoByEmail($ctx, $claims->email, $claims->registered_app);

        if ($cashier) {
            list($accessToken, $refreshToken) = Auth::generateTokensPair(
                Claims::cashierClaims($cashier)
            );
            sendJSON(200, [
                "access_token"     => $accessToken,
                "refresh_token"    => $refreshToken
            ]);
        }
    }

    sendErrJSON(404, ERR_USER_NOT_FOUND, "User not found");
}

/**
 * Send reset password request email.
 * @param string $email Email.
 * @param string $name Name.
 * @param string $otp OTP.
 */
function sendResetPasswordRequestEmail($email, $name, $otp)
{
    $subject = "Reset Your Password";
    $body = "Hi <b>{$name}</b><br><br>";
    $body .= "We've received a request to reset your password.<br>";
    $body .= "You can reset your password using this OTP <b>{$otp}</b><br><br>";
    $body .= "Thanks<br>";
    $body .= "Plat4m Inc.";
    $result = (new Mailer)->send($email, $name, $subject, $body);

    if ($result == 1) {
        sendMsgJSON(200, "An OTP has sent to the registered email.Please verify OTP for Force logout.");
    } if ($result == 0) {
        sendMsgJSON(404, "This email is not created");
    }

    sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR, "Failed to send OTP via email");
}

/**
 * Reset password request for admin or cashier.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function userResetPasswordRequestV1($ctx, $args)
{
    $payload = payload();
    $email = arrVal($payload, "email");

    $v = new Validator();
    $v->name("Email")->str($email)->reqStr()->strEmail();

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $otp = generateOTP();

    $adminModel = new Admin;
    $admin = $adminModel->getInfoByEmail($ctx, $email, $ctx->clientApp->name);

    if ($admin) {
        $insertID = (new AdminOTP)->create($ctx, [
            "email"             => $email,
            "otp"               => $otp,
            "created"           => $ctx->now,
            "registered_app"    => $ctx->clientApp->name,
            "event"             => EVENT_RESET_PASSWORD,
        ]);

        if (!$insertID) {
            Logger::errorMsg("OTP generation failed for admin [{$admin['id']}]");
            sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR, "OTP generation failed");
        }

        sendResetPasswordRequestEmail($email, $adminModel->getName($admin), $otp);
    }

    $cashierModel = new Cashier;
    $cashier = $cashierModel->getInfoByEmail($ctx, $email, $ctx->clientApp->name);

    if ($cashier) {
        $insertID = (new CashierOTP)->create($ctx, [
            "email"             => $email,
            "otp"               => $otp,
            "created"           => $ctx->now,
            "registered_app"    => $ctx->clientApp->name,
            "event"             => EVENT_RESET_PASSWORD
        ]);

        if (!$insertID) {
            Logger::errorMsg("OTP generation failed for cashier [{$cashier['id']}]");
            sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR, "OTP generation failed");
        }

        sendResetPasswordRequestEmail($email, $cashierModel->getName($cashier), $otp);
    }

    Logger::infoMsg("Email [{$email}] not found to send password reset email");
    sendErrJSON(400, ERR_USER_NOT_FOUND);
}

/**
 * Verify user OTP.
 * @param array $otpInfo OTP info.
 * @param string $inOTP OTP entered by user.
 * @return bool
 */
function validUserOTP($otpInfo, $inOTP, $lifeSpan)
{
    if (!$otpInfo) {
        return FALSE;
    }

    // Check if entered OTP and DB recorded OTP are same.
    if ($otpInfo["otp"] != $inOTP) {
        return FALSE;
    }

    // If OTP created time is future time, return TRUE.
    if (strtotime($otpInfo["created"]) > time()) {
        return TRUE;
    }

    // Calculate time difference between current time and OTP created time.
    $diffInMinutes = timeDiffInMins(date(DEFAULT_DATETIME_FMT), $otpInfo["created"]);

    // If difference is greater than limit, return FALSE.
    if ($diffInMinutes > $lifeSpan) {
        return FALSE;
    }

    return TRUE;
}

/**
 * Reset password redeem for admin or cashier.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function userResetPasswordV1($ctx, $args)
{
    $payload = payload();
    $input = [
        "email"     => arrVal($payload, "email"),
        "otp"       => (string) arrVal($payload, "otp", ""),
        "password"  => arrVal($payload, "password"),
    ];

    $v = new Validator();
    $v->name("Email")->str($input["email"])->reqStr()->strEmail();
    $v->name("Password")->str($input["password"])->reqStr()->minStr(8)->maxStr(16)->regularexp($input["password"]);
    $v->name("OTP")->str($input["otp"])->reqStr();

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $ctx->db->beginTransaction();

    $adminModel = new Admin;
    $admin = $adminModel->getInfoByEmail($ctx, $input["email"], $ctx->clientApp->name);

    if ($admin) {
        $adminOTPModel = new AdminOTP;
        $otpInfo = $adminOTPModel->getInfo(
            $ctx,
            $admin["email"],
            $admin["registered_app"],
            EVENT_RESET_PASSWORD
        );

        if (!validUserOTP($otpInfo, $input["otp"], RESET_PASSWORD_OTP_LIFE_SPAN)) {
            $ctx->db->rollBack();
            sendErrJSON(400, ERR_BAD_REQUEST, "OTP invalid or expired");
        }

        $updated = $adminModel->updatePassword(
            $ctx,
            pwHash($input["password"]),
            $admin["id"]
        );

        if ($updated) {
            $adminOTPModel->delete($ctx, $otpInfo["id"]);
            $ctx->db->commit();
            sendMsgJSON(200, "Password updated successfully");
        }

        $ctx->db->rollBack();
        sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR, "Failed to update password");
    }

    $cashierModel = new Cashier;
    $cashier = $cashierModel->getInfoByEmail($ctx, $input['email'], $ctx->clientApp->name);

    if ($cashier) {
        $cashierOTPModel = new CashierOTP;
        $otpInfo = $cashierOTPModel->getInfo(
            $ctx,
            $cashier["email"],
            $cashier["registered_app"],
            EVENT_RESET_PASSWORD
        );

        if (!validUserOTP($otpInfo, $input["otp"], RESET_PASSWORD_OTP_LIFE_SPAN)) {
            $ctx->db->rollBack();
            sendErrJSON(400, ERR_BAD_REQUEST, "OTP invalid or expired");
        }

        $updated = $cashierModel->updatePassword(
            $ctx,
            pwHash($input["password"]),
            $cashier["id"]
        );

        if ($updated) {
            $cashierOTPModel->delete($ctx, $otpInfo["id"]);
            $ctx->db->commit();
            sendMsgJSON(200, "Password updated successfully");
        }

        $ctx->db->rollBack();
        sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR, "Failed to update password");
    }

    $ctx->db->rollBack();
    sendErrJSON(400, ERR_USER_NOT_FOUND);
}

/**
 * Request Force login for admin or cashier.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function requestForceLogin($ctx, $args)
{
    $payload = payload();
    $email = arrVal($payload, "email");

    $v = new Validator();
    $v->name("Email")->str($email)->reqStr()->strEmail();

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $otp = generateOTP();

    $adminModel = new Admin;
    $admin = $adminModel->getInfoByEmail($ctx, $email, $ctx->clientApp->name);

    if ($admin) {
        $adminOTPModel = new AdminOTP();
        $insertID = $adminOTPModel->create($ctx, [
            "email"             => $email,
            "otp"               => $otp,
            "created"           => $ctx->now,
            "registered_app"    => $ctx->clientApp->name,
            "event"             => EVENT_FORCE_LOGIN,
        ]);

        if (!$insertID) {
            Logger::errorMsg("OTP generation failed for admin [{$email}] force login");
            sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR, "OTP generation failed");
        }

        sendResetPasswordRequestEmail($email, $adminModel->getName($admin), $otp);
    }

    $cashierModel = new Cashier;
    $cashier = $cashierModel->getInfoByEmail($ctx, $email, $ctx->clientApp->name);

    if ($cashier) {
        $cashierOTPModel = new CashierOTP();
        $insertID = $cashierOTPModel->create($ctx, [
            "email"             => $email,
            "otp"               => $otp,
            "created"           => $ctx->now,
            "registered_app"    => $ctx->clientApp->name,
            "event"             => EVENT_FORCE_LOGIN,
        ]);

        if (!$insertID) {
            Logger::errorMsg("OTP generation failed for cashier [{$cashier['id']}] force login");
            sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR, "OTP generation failed");
        }

        sendResetPasswordRequestEmail($email, $cashierModel->getName($cashier), $otp);
    }

    Logger::infoMsg("Email [{$email}] not found to send force login OTP");
    sendErrJSON(400, ERR_USER_NOT_FOUND);
}

/**
 * Force login for admin or cashier.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function forceLogin($ctx, $args)
{
    $payload = payload();
    $input = [
        "email"     => arrVal($payload, "email"),
        "otp"       => (string) arrVal($payload, "otp", ""),
    ];

    $v = new Validator();
    $v->name("Email")->str($input["email"])->reqStr()->strEmail();
    $v->name("OTP")->str($input["otp"])->reqStr();

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    forceAdminLogin($ctx, $input);
    forceCashierLogin($ctx, $input);

    sendErrJSON(400, ERR_USER_NOT_FOUND);
}

/**
 * Force login for admin.
 * @param object $ctx Context.
 * @param array $input Input read from payload.
 */
function forceAdminLogin($ctx, $input)
{
    $adminModel = new Admin;
    $admin = $adminModel->getInfoByEmail($ctx, $input["email"], $ctx->clientApp->name);

    if (!$admin) {
        return;
    }

    $adminOTPModel = new AdminOTP;
    $otpInfo = $adminOTPModel->getInfo(
        $ctx,
        $admin["email"],
        $admin["registered_app"],
        EVENT_FORCE_LOGIN
    );

    if (!validUserOTP($otpInfo, $input["otp"], FORCE_LOGIN_OTP_LIFE_SPAN)) {
        sendErrJSON(400, ERR_BAD_REQUEST, "OTP invalid or expired");
    }

    $ctx->db->beginTransaction();
    $adminLoginModel = new AdminLogin();
    $count = $adminLoginModel->deleteOther($ctx, $admin["id"], $admin["registered_app"]);
    Logger::infoMsg("Admin [{$admin['id']}] force login - deleted records count: {$count}");

    $adminLoginModel->create(
        $ctx,
        $admin["id"],
        $admin["registered_app"]
    );

    // Generate JWTs and populate Paytm credentials.
    list($accessToken, $refreshToken) = Auth::generateTokensPair(
        Claims::adminClaims($admin)
    );
    $paytmCredentials = empty($admin["paytm_credentials"])
        ? NULL
        : json_decode(base64_decode($admin["paytm_credentials"]));

    $response = [
        "id"                    => $admin["id"],
        "first_name"            => $admin["first_name"],
        "last_name"             => $admin["last_name"],
        "email"                 => $admin["email"],
        "mobile_number"         => $admin["mobile_number"],
        "admin_id"              => $admin["id"],
        "role"                  => $admin["role"],
        "paytm_credentials"     => $paytmCredentials,
        "currency"              => $admin["currency"],
        "currency_symbol"       => $admin["currency_symbol"],
        "access_token"          => $accessToken,
        "refresh_token"         => $refreshToken,
        "tax"                   => $admin["tax"],
    ];
    $ctx->db->commit();
    sendJSON(200, $response);
}

/**
 * Force login for cashier.
 * @param object $ctx Context.
 * @param array $input Input read from payload.
 */
function forceCashierLogin($ctx, $input)
{
    $cashierModel = new Cashier;
    $cashier = $cashierModel->getInfoByEmail($ctx, $input['email'], $ctx->clientApp->name);

    if (!$cashier) {
        return;
    }

    $cashierOTPModel = new CashierOTP;
    $otpInfo = $cashierOTPModel->getInfo(
        $ctx,
        $cashier["email"],
        $cashier["registered_app"],
        EVENT_FORCE_LOGIN
    );
    if (!validUserOTP($otpInfo, $input["otp"], FORCE_LOGIN_OTP_LIFE_SPAN)) {
        sendErrJSON(400, ERR_BAD_REQUEST, "OTP invalid or expired");
    }

    $ctx->db->beginTransaction();
    $cashierLoginModel = new CashierLogin();
    $count = $cashierLoginModel->deleteOther($ctx, $cashier["id"], $cashier["registered_app"]);
    Logger::infoMsg("Cashier [{$cashier['id']}] force login - deleted records count: {$count}");

    $cashierLoginModel->create(
        $ctx,
        $cashier["id"],
        $cashier["registered_app"]
    );

    // Generate JWTs and populate Paytm credentials.
    list($accessToken, $refreshToken) = Auth::generateTokensPair(
        Claims::cashierClaims($cashier)
    );

    $adminModel = new Admin;
    $admin = $adminModel->getInfoByID($ctx, $cashier["storeadmin_id"]);
    $currency = empty($cashier["currency"])
        ? DEFAULT_CURRENCY
        : $cashier["currency"];
    $currencySymbol = empty($cashier["currency_symbol"])
        ? DEFAULT_CURRENCY_SYMBOL
        : $cashier["currency_symbol"];
    $paytmCredentials = empty($admin["paytm_credentials"])
        ? NULL
        : json_decode(base64_decode($admin["paytm_credentials"]));

    $response = [
        "id"                    => $cashier["id"],
        "first_name"            => $cashier["first_name"],
        "last_name"             => $cashier["last_name"],
        "email"                 => $cashier["email"],
        "mobile_number"         => $admin["mobile_number"],
        "admin_id"              => $cashier["storeadmin_id"],
        "role"                  => USER_CASHIER,
        "paytm_credentials"     => $paytmCredentials,
        "currency"              => $currency,
        "currency_symbol"       => $currencySymbol,
        "access_token"          => $accessToken,
        "refresh_token"         => $refreshToken,
    ];
    $ctx->db->commit();
    sendJSON(200, $response);
}

/**
 * Verifies if device is valid.
 * @param object $ctx Context.
 */
function verifyDevice($ctx)
{
    $adminModel = new Admin;
    $admin = '';
    
    if ($ctx->clientApp->name === "OBCR") {
        $admin = $adminModel->getOBCRUserInfo($ctx, $ctx->tokenData->email, $ctx->clientApp->name);
    } else {
        $admin = $adminModel->getInfoByEmail($ctx, $ctx->tokenData->email, $ctx->clientApp->name);
    }

    if ($admin) {
        // Fetch instance IDs Of Admin.
        $adminLoginModel = new AdminLogin;
        $instanceIDs = $adminLoginModel->loggedInInstanceIDs(
            $ctx,
            $ctx->tokenData->id,
            $ctx->tokenData->registered_app
        );
        
        if(in_array($ctx->clientApp->instanceID, $instanceIDs)){
            return TRUE;
        }
    }

    $cashierModel = new Cashier;
    $cashier = $cashierModel->getInfoByEmail($ctx, $ctx->tokenData->email, $ctx->clientApp->name);

    if ($cashier) {
        // Fetch instance IDs Of Cashier.
        $cashierLoginModel = new CashierLogin;
        $instanceIDs = $cashierLoginModel->loggedInInstanceIDs(
            $ctx,
            $ctx->tokenData->id,
            $ctx->tokenData->registered_app
        );

        if(in_array($ctx->clientApp->instanceID, $instanceIDs)){
            return TRUE;
        }
    }

    if (!$admin && !$cashier) {
        sendErrJSON(400, ERR_LOGGEDIN_OTHER_DEVICE,"You are using wrong App.");
    }

    sendErrJSON(400, ERR_LOGGEDIN_OTHER_DEVICE);
        
}
/**
 * Logout handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function userLogoutV1($ctx, $args)
{
    sendEmpty(204);
}

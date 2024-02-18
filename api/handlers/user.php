<?php

use Plat4mAPI\Model\Admin;
use Plat4mAPI\Model\Cashier;
use Plat4mAPI\Util\Logger;
use Plat4mAPI\Util\Validator;
use Plat4mAPI\Model\ProductCatalogue;
use Plat4mAPI\Util\Mailer;
use Plat4mAPI\Model\EmailVerify;

/**
 * Create Admin handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function createAdminV1($ctx, $args)
{

    $payload = payload();
    $input = [
        // Mandatory fields.
        "first_name"        => arrVal($payload, "first_name"),
        "last_name"         => arrVal($payload, "last_name"),
        "email"             => arrVal($payload, "email"),
        "password"          => arrVal($payload, "password"),
        "mobile_number"     => arrVal($payload, "mobile_number"),
        "store_country"     => arrVal($payload, "store_country"),

        "store_state"       => arrVal($payload, "store_state"),
        "store_name"        => arrVal($payload, "store_name"),
        "street_address"    => arrVal($payload, "street_address"),
        "store_city"        => arrVal($payload, "store_city"),
        "store_zip"         => arrVal($payload, "store_zip"),

        // Optional fields.
        "store_type"        => arrVal($payload, "store_type"),
    ];

    // Validate input.
    $v = new Validator();
    $v->name("First name")->str($input["first_name"])->reqStr()->minStr(2)->maxStr(50);
    $v->name("Last name")->str($input["last_name"])->reqStr()->minStr(2)->maxStr(50);
    $v->name("Email")->str($input["email"])->reqStr()->strEmail();
    $v->name("Password")->str($input["password"])->reqStr()->minStr(8)->maxStr(16)->regularexp($input["password"]);
    $v->name("Mobile number")->str($input["mobile_number"])->reqStr();
    $v->name("Store country")->str($input["store_country"])->reqStr();

    $v->name("Store State")->str($input["store_state"])->reqStr();
    $v->name("Store Name")->str($input["store_name"])->reqStr();
    $v->name("Street Address")->str($input["street_address"])->reqStr();
    $v->name("Store City")->str($input["store_city"])->reqStr();
    $v->name("Store Zip")->str($input["store_zip"])->reqStr();

    if (!empty($input["store_type"])) {
        $v->name("Store type")->str($input["store_type"])->reqStr();
    }

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $adminModel = new Admin;
    $cashierModel = new Cashier;

    // Check if account already exists.
    $emailFound = $adminModel->emailFoundWithApp($ctx, $input['email'], $ctx->clientApp->name);
    $emailFound = $emailFound || $cashierModel->emailFoundWithApp($ctx, $input['email'], $ctx->clientApp->name);

    if ($emailFound) {
        sendErrJSON(400, ERR_ACCOUNT_EXISTS, "Email already exists");
    }

    // Create admin.
    list($currency, $currencySymbol) = currencyCodes($input["store_country"]);
    $adminID = $adminModel->create($ctx, [
        "name"                  => $input["first_name"] . " " . $input["last_name"],
        "first_name"            => $input["first_name"],
        "last_name"             => $input["last_name"],
        "email"                 => $input["email"],
        "password"              => $input["password"],
        "pwhash"                => pwHash($input["password"]),
        "user_img"              => "",
        "image_handel"          => 0,
        "currency"              => $currency,
        "currency_symbol"       => $currencySymbol,
        "tax"                   => 0.0,
        "shipping"              => "",
        "store_name"            => $input["store_name"],
        "street_address"        => $input["street_address"],
        "mobile_number"         => $input["mobile_number"],
        "created_on"            => $ctx->now,
        "modified_on"           => $ctx->now,
        "role"                  => USER_ADMIN,
        "status"                => 1,
        "paytm_credentials"     => NULL,
        "allowed_cashiers"      => 1,
        "registered_app"        => $ctx->clientApp->name,
        "allowed_logins"        => 1,
        "store_type"            => $input["store_type"],
        "store_city"            => $input["store_city"],
        "store_zip"             => $input["store_zip"],
        "store_country"         => $input["store_country"],
        "store_state"           => $input["store_state"],
    ]);

    if ($adminID) {

        //Add product of mystore category to the new user bydefault.
        $myStoreProduct = copyCatalogueProductToNewUser($ctx, $adminID, MYSTORE_CAT_ID);

        sendJSON(201, [
            "id"                => (int) $adminID,
            "first_name"        => (string) $input["first_name"],
            "last_name"         => (string) $input["last_name"],
            "email"             => (string) $input["email"],
            "password"          => (string) $input["password"],
            "mobile_number"     => (string) $input["mobile_number"],
            "store_type"        => (string) $input["store_type"],
            "store_name"        => (string) $input["store_name"],
            "street_address"    => (string) $input["street_address"],
            "store_city"        => (string) $input["store_city"],
            "store_zip"         => (string) $input["store_zip"],
            "store_country"     => (string) $input["store_country"],
            "store_state"       => (string) $input["store_state"],
            "currency"          => $currency,
            "currency_symbol"   => $currencySymbol,
        ]);
    } else {
        sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR, "Failed to create account");
    }
}

/**
 * Create Cashier handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function createCashierV1($ctx, $args)
{
    $payload = payload();
    $input = [
        // Mandatory fields.
        "email"             => arrVal($payload, "email"),
        "password"          => arrVal($payload, "password"),
        "first_name"        => arrVal($payload, "first_name"),
        "last_name"         => arrVal($payload, "last_name"),
        "mobile_number"     => arrVal($payload, "mobile_number"),
    ];

    // Validate input.
    $v = new Validator();
    $v->name("Email")->str($input["email"])->reqStr()->strEmail();
    $v->name("Password")->str($input["password"])->reqStr()->minStr(8)->maxStr(16)->regularexp($input["password"]);
    $v->name("First name")->str($input["first_name"])->reqStr()->minStr(2)->maxStr(50);
    $v->name("Last name")->str($input["last_name"])->reqStr()->minStr(2)->maxStr(50);
    $v->name("Mobile number")->str($input["mobile_number"])->reqStr();

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $adminModel = new Admin;
    $cashierModel = new Cashier;

    // Check if account already exists.
    $emailFound = $adminModel->emailFoundWithApp($ctx, $input['email'], $ctx->tokenData->registered_app);
    $emailFound = $emailFound || $cashierModel->emailFoundWithApp($ctx, $input['email'], $ctx->tokenData->registered_app);

    if ($emailFound) {
        sendErrJSON(400, ERR_ACCOUNT_EXISTS, "Email already exists");
    }

    // Verify if admin has access to create cashier.
    $admin = $adminModel->getInfoByID($ctx, $ctx->tokenData->id);
    if (!$admin) {
        sendErrJSON(400, ERR_FALSE_ADMIN);
    }

    $cashiersCount = $adminModel->getCashiersCount($ctx, $ctx->tokenData->id, $ctx->tokenData->registered_app);
    
    if ($cashiersCount >= $admin["allowed_cashiers"]) {
        sendErrJSON(400, ERR_CASHIERS_LIMIT_EXCEEDED);
    }

    // Create cashier.
    $cashierID = $cashierModel->create($ctx, [
        "first_name"        => $input["first_name"],
        "last_name"         => $input["last_name"],
        "username"          => NULL,
        "email"             => $input["email"],
        "password"          => pwHash($input["password"]),
        "mobile_number"     => $input["mobile_number"],
        "status"            => 1,
        "created_on"        => $ctx->now,
        "modified_on"       => $ctx->now,
        "storeadmin_id"     => $ctx->tokenData->id,
        "type_app_admin"    => USER_CASHIER,
        "registered_app"    => $ctx->tokenData->registered_app,
        "allowed_logins"    => 1,
    ]);

    if ($cashierID) {
        sendJSON(201, [
            "id"                => $cashierID,
            "first_name"        => $input["first_name"],
            "last_name"         => $input["last_name"],
            "email"             => $input["email"],
            "mobile_number"     => $input["mobile_number"],
            "store_admin_id"    => $ctx->tokenData->id,
        ], JSON_NUMERIC_CHECK);
    } else {
        sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR, "Failed to create account");
    }
}

/**
 * Admin profile handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function adminProfileV1($ctx, $args)
{
    $adminModel = new Admin;

    $admin = $adminModel->getInfoByID($ctx, $ctx->tokenData->id);
    
    if (!$admin) {
        sendErrJSON(401, ERR_USER_NOT_FOUND);
    }

    $paytmCredentials = empty($admin["paytm_credentials"])
        ? NULL
        : json_decode(base64_decode($admin["paytm_credentials"]));

    sendJSON(200, [
        "id"                    => $admin["id"],
        "first_name"            => $admin["first_name"],
        "last_name"             => $admin["last_name"],
        "email"                 => $admin["email"],
        "mobile_number"         => $admin["mobile_number"],
        "admin_id"              => $admin["id"],
        "role"                  => $admin["role"],
        "paytm_credentials"     => $paytmCredentials,
        "store_type"            => $admin["store_type"],
        "store_name"            => $admin["store_name"],
        "street_address"        => $admin["street_address"],
        "store_city"            => $admin["store_city"],
        "store_zip"             => $admin["store_zip"],
        "store_country"         => $admin["store_country"],
        "store_state"           => $admin["store_state"],
        "currency"              => $admin["currency"],
        "currency_symbol"       => $admin["currency_symbol"],
        "allowed_cashiers"      => $admin["allowed_cashiers"],
        "allowed_logins"        => $admin["allowed_logins"],
        "registered_app"        => $admin["registered_app"],
        "created_on"            => $admin["created_on"],
        "updated_on"            => $admin["modified_on"],
    ]);
}

/**
 * Cashier profile handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function cashierProfileV1($ctx, $args)
{
    $cashierModel = new Cashier;
    $cashier = $cashierModel->getInfoByID($ctx, $ctx->tokenData->id);

    if (!$cashier) {
        sendErrJSON(401, ERR_USER_NOT_FOUND);
    }

    sendJSON(200, [
        "id"                    => $cashier["id"],
        "first_name"            => $cashier["first_name"],
        "last_name"             => $cashier["last_name"],
        "email"                 => $cashier["email"],
        "mobile_number"         => $cashier["mobile_number"],
        "admin_id"              => $cashier["storeadmin_id"],
        "role"                  => $cashier["type_app_admin"],
        "registered_app"        => $cashier["registered_app"],
        "allowed_logins"        => $cashier["allowed_logins"],
        "created_on"            => $cashier["created_on"],
        "updated_on"            => $cashier["modified_on"],
    ]);
}

/**
 * Compares old and new admin attributes and updates them.
 * @param array $cur Current attributes.
 * @param array $new New attributes.
 * @return bool Changed or not.
 */
function changeAdminParams(&$cur, &$new)
{
    $changed = FALSE;

    if (!empty($new["first_name"]) && $cur["first_name"] != $new["first_name"]) {
        $cur["first_name"] = $new["first_name"];
        $changed = TRUE;
    }

    if (!empty($new["last_name"]) && $cur["last_name"] != $new["last_name"]) {
        $cur["last_name"] = $new["last_name"];
        $changed = TRUE;
    }

    if (!empty($new["mobile_number"]) && $cur["mobile_number"] != $new["mobile_number"]) {
        $cur["mobile_number"] = $new["mobile_number"];
        $changed = TRUE;
    }

    if (!empty($new["store_type"]) && $cur["store_type"] != $new["store_type"]) {
        $cur["store_type"] = $new["store_type"];
        $changed = TRUE;
    }

    if (!empty($new["store_name"]) && $cur["store_name"] != $new["store_name"]) {
        $cur["store_name"] = $new["store_name"];
        $changed = TRUE;
    }

    if (!empty($new["street_address"]) && $cur["street_address"] != $new["street_address"]) {
        $cur["street_address"] = $new["street_address"];
        $changed = TRUE;
    }

    if (!empty($new["store_city"]) && $cur["store_city"] != $new["store_city"]) {
        $cur["store_city"] = $new["store_city"];
        $changed = TRUE;
    }

    if (!empty($new["store_zip"]) && $cur["store_zip"] != $new["store_zip"]) {
        $cur["store_zip"] = $new["store_zip"];
        $changed = TRUE;
    }

    return $changed;
}

/**
 * Update admin handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function updateAdminV1($ctx, $args)
{
    $payload = payload();

    // Validate input.
    $v = new Validator();

    if (isset($payload["first_name"])) {
        $v->name("First name")->str($payload["first_name"])->reqStr()->minStr(2)->maxStr(50);
    }

    if (isset($payload["last_name"])) {
        $v->name("Last name")->str($payload["last_name"])->reqStr()->minStr(2)->maxStr(50);
    }

    if (isset($payload["mobile_number"])) {
        $v->name("Mobile number")->str($payload["mobile_number"])->reqStr();
    }

    if (isset($payload["store_type"])) {
        $v->name("Store type")->str($payload["store_type"])->reqStr();
    }

    if (isset($payload["store_name"])) {
        $v->name("Store name")->str($payload["store_name"])->reqStr();
    }

    if (isset($payload["street_address"])) {
        $v->name("Street address")->str($payload["street_address"])->reqStr();
    }

    if (isset($payload["store_city"])) {
        $v->name("Store city")->str($payload["store_city"])->reqStr();
    }

    if (isset($payload["store_zip"])) {
        $v->name("Store ZIP")->str($payload["store_zip"])->reqStr();
    }

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $adminModel = new Admin;
    $admin = $adminModel->getInfoByID($ctx, $ctx->tokenData->id);

    if (!$admin) {
        sendErrJSON(401, ERR_USER_NOT_FOUND);
    }

    $changed = changeAdminParams($admin, $payload);

    if (!$changed) {
        Logger::infoMsg("Attributes unchanged.");
    }

    $admin["name"]        = $admin["first_name"] . " " . $admin["last_name"];
    $admin["modified_on"] = $ctx->now;

    $updated = $adminModel->update($ctx, $admin);
    Logger::infoMsg(sprintf("Update admin count: %d", $updated));

    if ($updated) {
        sendMsgJSON(200, "Admin updated successfully");
    }

    sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR);
}

/**
 * Compares old and new cashiers attributes and updates them.
 * @param array $cur Current attributes.
 * @param array $new New attributes.
 * @return bool Changed or not.
 */
function changeCashierParams(&$cur, &$new)
{
    $changed = FALSE;

    if (!empty($new["first_name"]) && $cur["first_name"] != $new["first_name"]) {
        $cur["first_name"] = $new["first_name"];
        $changed = TRUE;
    }

    if (!empty($new["last_name"]) && $cur["last_name"] != $new["last_name"]) {
        $cur["last_name"] = $new["last_name"];
        $changed = TRUE;
    }

    if (!empty($new["mobile_number"]) && $cur["mobile_number"] != $new["mobile_number"]) {
        $cur["mobile_number"] = $new["mobile_number"];
        $changed = TRUE;
    }

    return $changed;
}

/**
 * Update cashier handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function updateCashierV1($ctx, $args)
{
    $payload = payload();

    // Validate input.
    $v = new Validator();

    if (isset($payload["first_name"])) {
        $v->name("First name")->str($payload["first_name"])->reqStr()->minStr(2)->maxStr(50);
    }

    if (isset($payload["last_name"])) {
        $v->name("Last name")->str($payload["last_name"])->reqStr()->minStr(2)->maxStr(50);
    }

    if (isset($payload["mobile_number"])) {
        $v->name("Mobile number")->str($payload["mobile_number"])->reqStr();
    }

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $cashierModel = new Cashier;
    $cashier = $cashierModel->getInfoByID($ctx, $ctx->tokenData->id);

    if (!$cashier) {
        sendErrJSON(401, ERR_USER_NOT_FOUND);
    }

    $changed = changeCashierParams($cashier, $payload);

    if (!$changed) {
        Logger::infoMsg("Attributes unchanged.");
    }

    $cashier["modified_on"]  = $ctx->now;

    $updated = $cashierModel->update($ctx, $cashier);
    Logger::infoMsg(sprintf("Update cashier count: %d", $updated));

    if ($updated) {
        sendMsgJSON(200, "Cashier updated successfully");
    }

    sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR);
}

/**
 * Update Paytm credentails handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function updatePaytmCredentailsV1($ctx, $args)
{
    $payload = payload();
    $input = [
        "mid"   => arrVal($payload, "mid"),
        "mkey"  => arrVal($payload, "mkey"),
    ];

    // Validate input.
    $v = new Validator();
    $v->name("Merchant ID")->str($input["mid"])->reqStr();
    $v->name("Merchant Key")->str($input["mkey"])->reqStr();

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $paytmCredentials = base64_encode(json_encode([
        "merchant_id" => $input["mid"],
        "merchant_key" => $input["mkey"]
    ]));

    $adminModel = new Admin;
    $updated = $adminModel->updatePaytmCredentials(
        $ctx,
        $paytmCredentials,
        $ctx->tokenData->id,
    );
    Logger::infoMsg("Updated Paytm credentials; Count: {$updated}");

    sendMsgJSON(200, "Paytm credentials updated successfully");
}

/**
 * Update admin password handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function adminPasswordChangeV1($ctx, $args)
{
    $payload = payload();
    $input = [
        "password"      => arrVal($payload, "password"),
        "new_password"  => arrVal($payload, "new_password"),
    ];

    // Validate input.
    $v = new Validator();
    $v->name("Current password")->str($input["password"])->reqStr();
    $v->name("New password")->str($input["new_password"])->reqStr()->minStr(8)->maxStr(16)->regularexp($input["new_password"]);

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $adminModel = new Admin;
    $admin = $adminModel->getInfoByID($ctx, $ctx->tokenData->id);
    if (!$admin) {
        sendErrJSON(400, ERR_FALSE_ADMIN);
    }

    if (!pwVerify($input["password"], $admin["pwhash"])) {
        sendErrJSON(401, ERR_UNAUTHORIZED, "Wrong current password");
    }

    $updated = $adminModel->updatePassword(
        $ctx,
        pwHash($input["new_password"]),
        $ctx->tokenData->id,
    );
    Logger::infoMsg("Updated Password; Count: {$updated}");

    sendMsgJSON(200, "Password updated successfully");
}

/**
 * Update cashier password handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function cashierPasswordChangeV1($ctx, $args)
{
    $payload = payload();
    $input = [
        "password"      => arrVal($payload, "password"),
        "new_password"  => arrVal($payload, "new_password"),
    ];

    // Validate input.
    $v = new Validator();
    $v->name("Current password")->str($input["password"])->reqStr();
    $v->name("New password")->str($input["new_password"])->reqStr()->minStr(8)->maxStr(16)->regularexp($input["new_password"]);

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $cashierModel = new Cashier;
    $cashier = $cashierModel->getInfoByID($ctx, $ctx->tokenData->id);
    if (!$cashier) {
        sendErrJSON(400, ERR_FASLE_CASHIER);
    }

    if (!pwVerify($input["password"], $cashier["password"])) {
        sendErrJSON(401, ERR_UNAUTHORIZED, "Wrong current password");
    }

    $updated = $cashierModel->updatePassword(
        $ctx,
        pwHash($input["new_password"]),
        $ctx->tokenData->id,
    );
    Logger::infoMsg("Updated Password; Count: {$updated}");

    sendMsgJSON(200, "Password updated successfully");
}

/**
 * Fetch all cashiers created by admin handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function getAllAdminCashiersV1($ctx, $args)
{
    $adminModel = new Admin;
    $admin = $adminModel->getInfoByID($ctx, $ctx->tokenData->id);
    $cashiers = $adminModel->getCashiers($ctx, $ctx->tokenData->id, $ctx->tokenData->registered_app);
    $formattedCashiers = [];

    foreach ($cashiers as $cashier) {
        $formattedCashiers[] = [
            "id"                => $cashier["id"],
            "first_name"        => $cashier["first_name"],
            "last_name"         => $cashier["last_name"],
            "email"             => $cashier["email"],
            "storeadmin_id"     => $cashier["storeadmin_id"],
            "type_app_admin"    => $cashier["type_app_admin"]
        ];
    }

    sendJSON(200, [
        "admin"   => $admin,
        "cashier" => $formattedCashiers
    ]);
}

/** 
 * Change status for delete cashier
 * @param object $ctx Context.
 * @param array $args Argument.
 */
function deleteCashier($ctx, $args)
{
   $cashierModel = new Cashier;
   $cashierExist = $cashierModel->userExists($ctx,$args["cashier_id"]);
   if (!$cashierExist) {
       sendErrJSON(401, ERR_USER_NOT_FOUND);
   }

   $statusUpdate = $cashierModel->statusUpdate(
        $ctx,
        $args["cashier_id"],
        DELETE_CASHIER    
    );
   sendMsgJSON(200, "User Deleted Successfully");
}

/**
 * Copy product of MyStore category to new store user.
 * @param object $ctx Context.
 * @param Int $adminID id.
 * @param Int $category_id MyStore cat_id.
 */
function copyCatalogueProductToNewUser($ctx, $adminID,$category_id)
{
    $myStoreProduct = (new ProductCatalogue)->getMyStoreCatProduct($ctx,$category_id);

    foreach ($myStoreProduct as $key => $v) {

        // Fetch product from catalogue.
        $productModel = new ProductCatalogue;
        $product = $productModel->getInfoByUPC($ctx, $v["upc"]);
        if (!$product) {
             Logger::infoMsg(sprintf("Product Not Found: %d", count($product)));
        }

        // Update dynamic fields and copy product details from catalogue to store.
        $product["admin_id"]               = $adminID;
        $product["Date_Created"]           = $ctx->now;
        $product["created_date_on"]        = $ctx->now;
        $product["created_on"]             = $ctx->now;
        $product["updated_on"]             = $ctx->now;

        $insertID = $productModel->createDetails($ctx, $product);
        if (!$insertID) {
            Logger::infoMsg(sprintf("Failed to move product from catalogue to new store: %d", count($insertID)));
        }

        Logger::infoMsg(sprintf("copy product from catalogue to new store: %d", $adminID));
    }

}

/**
 * Update Store admin.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function updateStoreAdminV1($ctx, $args)
{
    $payload = payload();

    // Validate input.
    $v = new Validator();

    if (isset($payload["tax"])) {
        $v->name("Store Tax")->str($payload["tax"])->reqStr();
    }

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $adminModel = new Admin;
    $admin = $adminModel->getInfoByID($ctx, $ctx->tokenData->store_admin_id);

    if (!$admin) {
        sendErrJSON(401, ERR_USER_NOT_FOUND);
    }

    $admin["modified_on"] = $ctx->now;
    $admin["tax"] = isset($payload["tax"]) ? $payload["tax"] :  $admin["tax"];

    $updated = $adminModel->update($ctx, $admin);
    Logger::infoMsg(sprintf("Update admin count: %d", $updated));

    if ($updated) {
        sendMsgJSON(200, "Store Tax updated successfully");
    }

    sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR);
}


/**
 * Send otp to verify email.
 * @param string $email Email.
 * @param string $otp OTP.
 */
function sendVeriftOTPRequestEmail($email,$name,$otp)
{
    $subject = "Verify your email";
    $body = "Hi <b>{$name}</b><br><br>";
    $body .= "We've received a request to verify your mail.<br>";
    $body .= "You can validate email using this OTP <b>{$otp}</b><br><br>";
    $body .= "Thanks<br>";
    $body .= "Plat4m Inc.";
    $result = (new Mailer)->send($email, $name, $subject, $body);

    if ($result == 1) {
        sendMsgJSON(200, "An OTP has sent to the registered email.Please validate OTP.");
    } if ($result == 0) {
        sendMsgJSON(404, "This email is not exists. Please enter valid email");
    }

    sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR, "Failed to send OTP via email");
}

/**
 * Verify email handler.
 * @param object $ctx Context.
 * @param string $args Arguments.
 */
function verifyEmail($ctx, $args)
{
    $payload = payload();
    $email = arrVal($payload, "email");

    // Validate input.
    $v = new Validator();
    $v->name("Email")->str($email)->reqStr()->strEmail();

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $adminModel = new Admin;
    $cashierModel = new Cashier;

    // Check if account already exists.
    $emailFound = $adminModel->emailFoundWithApp($ctx, $email, $ctx->clientApp->name);
    $emailFound = $emailFound || $cashierModel->emailFoundWithApp($ctx, $email, $ctx->clientApp->name);

    if ($emailFound) {
        sendErrJSON(400, ERR_ACCOUNT_EXISTS, "Email already exists");
    }
    // Send otp to verify email
    $otp = generateOTP();

        $emailVerifyModel = new EmailVerify();
        $insertID = $emailVerifyModel->create($ctx, [
            "email"             => $email,
            "otp"               => $otp,
            "created"           => $ctx->now,
            "event"             => "verify-email",
        ]);

        if (!$insertID) {
            Logger::errorMsg("OTP generation failed for user [{$email}] verify");
            sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR, "OTP generation failed");
        }

    sendVeriftOTPRequestEmail($email,"User", $otp);
}

/**
 * Validate email OTP.
 * @param object $ctx Context.
 * @param array $input Input read from payload.
 */
function validateEmailWithOTP($ctx, $input)
{
    $payload = payload();
    $input = [
        "email"     => arrVal($payload, "email"),
        "otp"       => (string) arrVal($payload, "otp")
    ];

    $v = new Validator();
    $v->name("Email")->str($input["email"])->reqStr()->strEmail();
    $v->name("OTP")->str($input["otp"])->reqStr();

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $emailValidateModel = new EmailVerify();
    $otpInfo = $emailValidateModel->getInfo(
        $ctx,
        $input["email"],
        "verify-email"
    );

    if (!validateOTP($otpInfo, $input["otp"], VERIFY_EMAIL_OTP_LIFE_SPAN)) {
        sendErrJSON(400, ERR_BAD_REQUEST, "OTP invalid or expired");
    }

    sendJSON(200, ["message" => "Verified OTP"]);
}

/**
 * Validate email verify OTP.
 * @param array $otpInfo OTP info.
 * @param string $inOTP OTP entered by user.
 * @return bool
 */
function validateOTP($otpInfo, $inOTP, $lifeSpan)
{
    if (!$otpInfo) {
        return FALSE;
    }

    // Check if entered OTP and DB recorded OTP are same.
    if ($otpInfo["otp"] != $inOTP) {
        return FALSE;
    }

    // If OTP created time is future time, return TRUE.
    if (strtotime($otpInfo["created_on"]) > time()) {
        return TRUE;
    }

    // Calculate time difference between current time and OTP created time.
    $diffInMinutes = timeDiffInMins(date(DEFAULT_DATETIME_FMT), $otpInfo["created_on"]);

    // If difference is greater than limit, return FALSE.
    if ($diffInMinutes > $lifeSpan) {
        return FALSE;
    }

    return TRUE;
}
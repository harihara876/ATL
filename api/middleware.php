<?php

use Plat4mAPI\App\Auth;

/**
 * Returns client application information found in headers.
 * @return object.
 */
function clientAppInfo()
{
    $headers = getallheaders();

    $clientApp = new stdClass;
    $clientApp->name = arrVal($headers, HEADER_APP_NAME, "OBCR");
    $clientApp->instanceID = arrVal($headers, HEADER_APP_INSTANCE_ID, "");
    $clientApp->device = arrVal($headers, HEADER_APP_DEVICE, "");
    $clientApp->version = arrVal($headers, HEADER_APP_VERSION, "");
    $clientApp->platform = arrVal($headers, HEADER_APP_PLATFORM, "");
    $clientApp->userAgent = arrVal($headers, "User-Agent", "");

    return $clientApp;
}

/**
 * Verifies user authentication.
 * @return array Token data.
 * @throws Exception
 */
function verifyAuth()
{
    $token = getAuthBearerToken();
    if (!$token) {
        sendErrJSON(401, ERR_UNAUTHORIZED);
    }

    $status = Auth::verifyJWT(
        $token,
        JWT_SECRET,
        JWT_ISSUER,
        JWT_SIGN_ALGO,
        JWT_TYPE_ACCESS,
        $tokenData
    );
    if ($status != HTTP_STATUS_OK) {
        sendErrJSON(401, ERR_UNAUTHORIZED);
    }

    return $tokenData;
}

/**
 * Check if route has admin access only.
 * @param object $ctx Context.
 * @return bool
 */
function adminOnlyAccess($ctx)
{
    return $ctx->tokenData->type == USER_ADMIN;
}

/**
 * Check if route has cashier access only.
 * @param object $ctx Context.
 * @return bool
 */
function cashierOnlyAccess($ctx)
{
    return $ctx->tokenData->type == USER_CASHIER;
}

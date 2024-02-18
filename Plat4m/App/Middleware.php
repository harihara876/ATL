<?php

namespace Plat4m\App;

use Exception;
use Plat4m\Utilities\Request;

class Middleware
{
    /**
     * Verifies user authentication.
     * @return array Token data.
     * @throws Exception
     */
    public static function verifyAuth()
    {
        $status = Auth::verifyJWT(
            Request::getAuthBearerToken(),
            JWT_SECRET,
            JWT_ISSUER,
            JWT_SIGN_ALGO,
            JWT_TYPE_ACCESS,
            $tokenData
        );
        if ($status != HTTP_STATUS_OK) {
            throw new Exception("Failed to authenticate", $status);
        }

        return $tokenData;
    }
}

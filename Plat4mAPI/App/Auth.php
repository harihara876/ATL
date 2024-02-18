<?php

namespace Plat4mAPI\App;

use DateTimeImmutable;
use Exception;
use Firebase\JWT\JWT;
use Plat4m\Utilities\Logger;

class Auth
{
    /**
     * Generates JWT.
     * @param string $issuer Issuer.
     * @param string $secret JWT secret.
     * @param string $algo Algorithm to sign JWT.
     * @param int $expirySecs Expiry in seconds.
     * @param array Data to send.
     * @return string JWT.
     */
    public static function generateJWT(
        $issuer,
        $secret,
        $algo,
        $expirySecs,
        $tokenType,
        $data
    )
    {
        $tokenId    = base64_encode(random_bytes(16));
        $issuedAt   = new DateTimeImmutable();
        $expiry     = $issuedAt->modify("+{$expirySecs} seconds")->getTimestamp();
        $claims = [
            "iat"  => $issuedAt->getTimestamp(),
            "jti"  => $tokenId,
            "iss"  => $issuer,
            "nbf"  => $issuedAt->getTimestamp(),
            "exp"  => $expiry,
            "type" => $tokenType,
            "data" => $data
        ];

        return JWT::encode($claims, $secret, $algo);
    }

    /**
     * Generates access and refresh tokens pair.
     * @param array $data Data to send.
     * @return array Access and refresh tokens.
     */
    public static function generateTokensPair($data)
    {
        $accessToken = self::generateJWT(
            JWT_ISSUER,
            JWT_SECRET,
            JWT_SIGN_ALGO,
            JWT_ACCESS_TOKEN_EXPIRY_SECS,
            JWT_TYPE_ACCESS,
            $data
        );
        $refreshToken = self::generateJWT(
            JWT_ISSUER,
            JWT_SECRET,
            JWT_SIGN_ALGO,
            JWT_REFRESH_TOKEN_EXPIRY_SECS,
            JWT_TYPE_REFRESH,
            $data
        );

        return [$accessToken, $refreshToken];
    }

    /**
     * Verifies JWT.
     * @param string $jwt JWT.
     * @param string $secret JWT secret.
     * @param string $issuer Issuer.
     * @param string $algo Algorithm.
     * @param mixed Pointer to payload.
     * @return int Status code.
     */
    public static function verifyJWT($jwt, $secret, $issuer, $algo, $tokenType, &$payload)
    {
        try {
            $claims = JWT::decode($jwt, $secret, [$algo]);
            $now = new DateTimeImmutable();

            if ($claims->iss != $issuer) {
                Logger::errorMsg(sprintf("Unknown JWT issuer: %s", $claims->iss));
                return HTTP_STATUS_UNAUTHORIZED;
            }

            if ($claims->nbf > $now->getTimestamp()) {
                Logger::errorMsg(sprintf("Invalid JWT. NBF: %s", $claims->nbf));
                return HTTP_STATUS_UNAUTHORIZED;
            }

            if ($claims->exp < $now->getTimestamp()) {
                Logger::errorMsg(sprintf("JWT expired. EXP: %s", $claims->exp));
                return HTTP_STATUS_UNAUTHORIZED;
            }

            if ($claims->type != $tokenType) {
                Logger::errorMsg(sprintf("Unexpected JWT type '%s', expected '%s'", $claims->type, $tokenType));
                return HTTP_STATUS_UNAUTHORIZED;
            }

            $payload = $claims->data;

            return HTTP_STATUS_OK;
        } catch (Exception $ex) {
            Logger::errExcept($ex);
            return HTTP_STATUS_UNAUTHORIZED;
        }
    }
}

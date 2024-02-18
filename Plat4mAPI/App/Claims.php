<?php

namespace Plat4mAPI\App;

class Claims
{
    /**
     * Returns admin claims to be used inside JWT.
     * @param array $data Admin info.
     * @return array Admin claims to be used inside JWT.
     */
    public static function adminClaims($data)
    {
        return [
            "id"                => $data["id"],
            "name"              => $data["name"],
            "email"             => $data["email"],
            "store_admin_id"    => $data["id"],
            "type"              => USER_ADMIN,
            "registered_app"    => $data["registered_app"],
        ];
    }

    /**
     * Returns cashier claims to be used inside JWT.
     * @param array $data Cashier info.
     * @return array Cashier claims to be used inside JWT.
     */
    public static function cashierClaims($data)
    {
        return [
            "id"                => $data["id"],
            "name"              => $data["first_name"],
            "email"             => $data["email"],
            "store_admin_id"    => $data["storeadmin_id"],
            "type"              => USER_CASHIER,
            "registered_app"    => $data["registered_app"],
        ];
    }
}

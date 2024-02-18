<?php

namespace Plat4m\App;

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
            "id"                => $data["admin_id"],
            "name"              => $data["name"],
            "email"             => $data["email"],
            "store_admin_id"    => $data["admin_id"],
            "type"              => $data["type_appstatus"],
            "registered_app"    => $data["registered_app"],
        ];
    }

    /**
     * Returns device user claims to be used inside JWT.
     * @param array $data Device user info.
     * @return array Device user claims to be used inside JWT.
     */
    public static function deviceUserClaims($data)
    {
        return [
            "id"                => $data["id"],
            "name"              => $data["first_name"],
            "email"             => $data["email"],
            "store_admin_id"    => $data["storeadmin_id"],
            "type"              => "DeviceUser",
            "registered_app"    => $data["registered_app"],
        ];
    }
}

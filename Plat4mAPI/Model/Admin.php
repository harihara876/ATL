<?php

namespace Plat4mAPI\Model;

use PDO;

class Admin
{
    /**
     * Format admin info.
     * @param array $admin Admin info.
     * @return array Formatted info.
     */
    public function format(&$admin)
    {
        if (!$admin) {
            return NULL;
        }

        return [
            "id"                    => $admin["id"],
            "first_name"            => $admin["first_name"],
            "last_name"             => $admin["last_name"],
            "email"                 => $admin["email"],
            "password"              => $admin["password"],
            "mobile_number"         => $admin["mobile_number"],
            "store_type"            => $admin["store_type"],
            "store_name"            => $admin["store_name"],
            "street_address"        => $admin["street_address"],
            "store_city"            => $admin["store_city"],
            "store_zip"             => $admin["store_zip"],
            "store_country"         => $admin["store_country"],
            "store_state"           => $admin["store_state"],
            "currency"              => $admin["currency"],
            "currency_symbol"       => $admin["currency_symbol"],
            "paytm_credentials"     => $admin["paytm_credentials"],
            "created_on"            => $admin["created_on"],
            "updated_on"            => $admin["updated_on"],
        ];
    }

    /**
     * Create admin.
     * @param object $ctx Context.
     * @param array $admin Admin info.
     * @return int Last insert ID.
     */
    public function create($ctx, $admin)
    {
        $insertSQL = "INSERT INTO `admin` (
            `name`,
            `first_name`,
            `last_name`,
            `email`,
            `password`,
            `pwhash`,
            `user_img`,
            `image_handel`,
            `currency`,
            `currency_symbol`,
            `tax`,
            `shipping`,
            `store_name`,
            `address`,
            `phone`,
            `created_on`,
            `modified_on`,
            `type_appstatus`,
            `status`,
            `paytm_credentials`,
            `allowed_cashiers`,
            `registered_app`,
            `allowed_logins`,
            `store_type`,
            `store_city`,
            `store_zip`,
            `store_country`,
            `store_state`
        ) VALUES (
            :name,
            :first_name,
            :last_name,
            :email,
            :password,
            :pwhash,
            :user_img,
            :image_handel,
            :currency,
            :currency_symbol,
            :tax,
            :shipping,
            :store_name,
            :street_address,
            :mobile_number,
            :created_on,
            :modified_on,
            :role,
            :status,
            :paytm_credentials,
            :allowed_cashiers,
            :registered_app,
            :allowed_logins,
            :store_type,
            :store_city,
            :store_zip,
            :store_country,
            :store_state
        )";
        $stmt = $ctx->db->prepare($insertSQL);
        $stmt->bindValue(":name", $admin["name"]);
        $stmt->bindValue(":first_name", $admin["first_name"]);
        $stmt->bindValue(":last_name", $admin["last_name"]);
        $stmt->bindValue(":email", $admin["email"]);
        $stmt->bindValue(":password", $admin["password"]);
        $stmt->bindValue(":pwhash", $admin["pwhash"]);
        $stmt->bindValue(":user_img", $admin["user_img"]);
        $stmt->bindValue(":image_handel", $admin["image_handel"]);
        $stmt->bindValue(":currency", $admin["currency"]);
        $stmt->bindValue(":currency_symbol", $admin["currency_symbol"]);
        $stmt->bindValue(":tax", $admin["tax"]);
        $stmt->bindValue(":shipping", $admin["shipping"]);
        $stmt->bindValue(":store_name", $admin["store_name"]);
        $stmt->bindValue(":street_address", $admin["street_address"]);
        $stmt->bindValue(":mobile_number", $admin["mobile_number"]);
        $stmt->bindValue(":created_on", $admin["created_on"]);
        $stmt->bindValue(":modified_on", $admin["modified_on"]);
        $stmt->bindValue(":role", $admin["role"]);
        $stmt->bindValue(":status", $admin["status"]);
        $stmt->bindValue(":paytm_credentials", $admin["paytm_credentials"]);
        $stmt->bindValue(":allowed_cashiers", $admin["allowed_cashiers"]);
        $stmt->bindValue(":registered_app", $admin["registered_app"]);
        $stmt->bindValue(":allowed_logins", $admin["allowed_logins"]);
        $stmt->bindValue(":store_type", $admin["store_type"]);
        $stmt->bindValue(":store_city", $admin["store_city"]);
        $stmt->bindValue(":store_zip", $admin["store_zip"]);
        $stmt->bindValue(":store_country", $admin["store_country"]);
        $stmt->bindValue(":store_state", $admin["store_state"]);
        $stmt->execute();

        return $ctx->db->lastInsertId();
    }

    /**
     * Fetch admin info by ID.
     * @param object $ctx Context.
     * @param int $id Admin ID.
     * @return array Admin info.
     */
    public function getInfoByID($ctx, $id)
    {
        $selectSQL = "SELECT
                `admin_id` AS `id`,
                `name`,
                `first_name`,
                `last_name`,
                `email`,
                `password`,
                `pwhash`,
                `user_img`,
                `image_handel`,
                `currency`,
                `currency_symbol`,
                `tax`,
                `shipping`,
                `store_name`,
                `address` AS `street_address`,
                `phone` AS `mobile_number`,
                `created_on`,
                `modified_on`,
                `type_appstatus` AS `role`,
                `status`,
                `paytm_credentials`,
                `allowed_cashiers`,
                `registered_app`,
                `allowed_logins`,
                `store_type`,
                `store_city`,
                `store_zip`,
                `store_country`,
                `store_state`
            FROM `admin`
            WHERE `admin_id` = :id";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch admin info by ID.
     * @param object $ctx Context.
     * @param int $id Admin ID.
     * @param string $registeredApp Registered app.
     * @return array Admin info.
     */
    public function getInfoByEmail($ctx, $email, $registeredApp)
    {
        $selectSQL = "SELECT
                `admin_id` AS `id`,
                `name`,
                `first_name`,
                `last_name`,
                `email`,
                `password`,
                `pwhash`,
                `user_img`,
                `image_handel`,
                `currency`,
                `currency_symbol`,
                `tax`,
                `shipping`,
                `store_name`,
                `address` AS `street_address`,
                `phone` AS `mobile_number`,
                `created_on`,
                `modified_on`,
                `type_appstatus` AS `role`,
                `status`,
                `paytm_credentials`,
                `allowed_cashiers`,
                `registered_app`,
                `allowed_logins`,
                `store_type`,
                `store_city`,
                `store_zip`,
                `store_country`,
                `store_state`
            FROM `admin`
            WHERE `email` = :email
            AND `registered_app` = :registered_app";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":email", $email);
        $stmt->bindValue(":registered_app", $registeredApp);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

        /**
     * Fetch admin info by ID.
     * @param object $ctx Context.
     * @param int $id OBCR User ID.
     * @return array OBCR User info.
     */
    public function getOBCRUserInfo($ctx, $email)
    {
        $selectSQL = "SELECT
                `admin_id` AS `id`,
                `name`,
                `first_name`,
                `last_name`,
                `email`,
                `password`,
                `pwhash`,
                `user_img`,
                `image_handel`,
                `currency`,
                `currency_symbol`,
                `tax`,
                `shipping`,
                `store_name`,
                `address` AS `street_address`,
                `phone` AS `mobile_number`,
                `created_on`,
                `modified_on`,
                `type_appstatus` AS `role`,
                `status`,
                `paytm_credentials`,
                `allowed_cashiers`,
                `registered_app`,
                `allowed_logins`,
                `store_type`,
                `store_city`,
                `store_zip`,
                `store_country`,
                `store_state`
            FROM `admin`
            WHERE `email` = :email";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":email", $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get admin name.
     * @param object $admin Admin info.
     * @return string Name.
     */
    public function getName(&$admin)
    {
        // TODO: Remove the below condition and always use full name.
        return empty($admin["name"])
            ? $admin["first_name"] . " " . $admin["last_name"]
            : $admin["name"];
    }

    /**
     * Update admin.
     * @param object $ctx Context.
     * @param array $admin Admin info.
     * @return int Row count.
     */
    public function update($ctx, $admin)
    {
        $updateSQL = "UPDATE `admin` SET
                `name` = :name,
                `first_name` = :first_name,
                `last_name` = :last_name,
                `email` = :email,
                `user_img` = :user_img,
                `image_handel` = :image_handel,
                `currency` = :currency,
                `currency_symbol` = :currency_symbol,
                `tax` = :tax,
                `shipping` = :shipping,
                `store_name` = :store_name,
                `address` = :street_address,
                `phone` = :mobile_number,
                `created_on` = :created_on,
                `modified_on` = :modified_on,
                `type_appstatus` = :role,
                `status` = :status,
                `allowed_cashiers` = :allowed_cashiers,
                `registered_app` = :registered_app,
                `allowed_logins` = :allowed_logins,
                `store_type` = :store_type,
                `store_city` = :store_city,
                `store_zip` = :store_zip,
                `store_country` = :store_country
            WHERE `admin_id` = :id";
        $stmt = $ctx->db->prepare($updateSQL);
        $stmt->bindValue(":name", $admin["name"]);
        $stmt->bindValue(":first_name", $admin["first_name"]);
        $stmt->bindValue(":last_name", $admin["last_name"]);
        $stmt->bindValue(":email", $admin["email"]);
        $stmt->bindValue(":user_img", $admin["user_img"]);
        $stmt->bindValue(":image_handel", $admin["image_handel"]);
        $stmt->bindValue(":currency", $admin["currency"]);
        $stmt->bindValue(":currency_symbol", $admin["currency_symbol"]);
        $stmt->bindValue(":tax", $admin["tax"]);
        $stmt->bindValue(":shipping", $admin["shipping"]);
        $stmt->bindValue(":store_name", $admin["store_name"]);
        $stmt->bindValue(":street_address", $admin["street_address"]);
        $stmt->bindValue(":mobile_number", $admin["mobile_number"]);
        $stmt->bindValue(":created_on", $admin["created_on"]);
        $stmt->bindValue(":modified_on", $admin["modified_on"]);
        $stmt->bindValue(":role", $admin["role"]);
        $stmt->bindValue(":status", $admin["status"]);
        $stmt->bindValue(":allowed_cashiers", $admin["allowed_cashiers"]);
        $stmt->bindValue(":registered_app", $admin["registered_app"]);
        $stmt->bindValue(":allowed_logins", $admin["allowed_logins"]);
        $stmt->bindValue(":store_type", $admin["store_type"]);
        $stmt->bindValue(":store_city", $admin["store_city"]);
        $stmt->bindValue(":store_zip", $admin["store_zip"]);
        $stmt->bindValue(":store_country", $admin["store_country"]);
        $stmt->bindValue(":id", $admin["id"]);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Update admin password.
     * @param object $ctx Context.
     * @param string $pwHash Hashed password.
     * @param int $id Admin ID.
     * @return int Row count.
     */
    public function updatePassword($ctx, $pwHash, $id)
    {
        $updateSQL = "UPDATE `admin`
            SET `pwhash` = :pwhash,
                `modified_on` = :modified_on
            WHERE `admin_id` = :id";
        $stmt = $ctx->db->prepare($updateSQL);
        $stmt->bindValue(":pwhash", $pwHash);
        $stmt->bindValue(":modified_on", $ctx->now);
        $stmt->bindValue(":id", $id);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Update admin Paytm credentials.
     * @param object $ctx Context.
     * @param string $paytmCredentials Paytm credentials.
     * @param int $id Admin ID.
     * @return int Row count.
     */
    public function updatePaytmCredentials($ctx, $paytmCredentials, $id)
    {
        $updateSQL = "UPDATE `admin`
            SET `paytm_credentials` = :paytm_credentials,
                `modified_on` = :modified_on
            WHERE `admin_id` = :id";
        $stmt = $ctx->db->prepare($updateSQL);
        $stmt->bindValue(":paytm_credentials", $paytmCredentials);
        $stmt->bindValue(":modified_on", $ctx->now);
        $stmt->bindValue(":id", $id);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Check if email exists.
     * @param object $ctx Context.
     * @param string $email Eamil.
     * @return bool Exists or not.
     * @throws Exception
     */
    public function emailFound($ctx, $email)
    {
        $selectSQL = "SELECT EXISTS(
            SELECT * FROM `admin` WHERE `email` = :email
        ) AS `email_found`";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Check if email exists with app name.
     * @param object $ctx Context.
     * @param string $email Eamil.
     * @param string $appName Registered app name.
     * @return bool Exists or not.
     * @throws Exception
     */
    public function emailFoundWithApp($ctx, $email, $appName)
    {
        $selectSQL = "SELECT EXISTS(
            SELECT * FROM `admin`
                WHERE `email` = :email
                AND `registered_app` = :registered_app
        ) AS `email_found`";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->bindValue(":registered_app", $appName, PDO::PARAM_STR);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Fetch number of cashiers created by the admin.
     * @param object $ctx Context.
     * @param int $adminID Admin ID.
     * @param string $appName Registered app name.
     * @return int Count of cashiers.
     */
    public function getCashiersCount($ctx, $adminID, $appName)
    {
        $selectSQL = "SELECT COUNT(*)
            FROM `device_users`
            WHERE `storeadmin_id` = :storeadmin_id
            AND `registered_app` = :registered_app";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":storeadmin_id", $adminID, PDO::PARAM_INT);
        $stmt->bindValue(":registered_app", $appName, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Fetches device users of a store by store admin ID.
     * @param int $storeAdminID Store admin ID.
     * @return array Device users.
     * @throws Exception
     */
    public function getCashiers($ctx, $adminID, $appName)
    {
        $selectSQL = "SELECT * FROM `device_users`
            WHERE `storeadmin_id` = :storeadmin_id
            AND `registered_app` = :registered_app";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":storeadmin_id", $adminID, PDO::PARAM_INT);
        $stmt->bindValue(":registered_app", $appName, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

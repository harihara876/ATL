<?php

namespace Plat4mAPI\Model;

use PDO;

class Cashier
{
    /**
     * Create cashier.
     * @param object $ctx Context.
     * @param array $cashier Cashier info.
     * @return int Last insert ID.
     */
    public function create($ctx, $cashier)
    {
        $insertSQL = "INSERT INTO `device_users` (
            `first_name`,
            `last_name`,
            `username`,
            `email`,
            `password`,
            `status`,
            `mobile_number`,
            `created_on`,
            `modified_on`,
            `storeadmin_id`,
            `type_app_admin`,
            `registered_app`,
            `allowed_logins`
        ) VALUES (
            :first_name,
            :last_name,
            :username,
            :email,
            :password,
            :status,
            :mobile_number,
            :created_on,
            :modified_on,
            :storeadmin_id,
            :type_app_admin,
            :registered_app,
            :allowed_logins
        )";
        $stmt = $ctx->db->prepare($insertSQL);
        $stmt->bindValue(":first_name", $cashier["first_name"]);
        $stmt->bindValue(":last_name", $cashier["last_name"]);
        $stmt->bindValue(":username", $cashier["username"]);
        $stmt->bindValue(":email", $cashier["email"]);
        $stmt->bindValue(":password", $cashier["password"]);
        $stmt->bindValue(":status", $cashier["status"]);
        $stmt->bindValue(":mobile_number", $cashier["mobile_number"]);
        $stmt->bindValue(":created_on", $cashier["created_on"]);
        $stmt->bindValue(":modified_on", $cashier["modified_on"]);
        $stmt->bindValue(":storeadmin_id", $cashier["storeadmin_id"]);
        $stmt->bindValue(":type_app_admin", $cashier["type_app_admin"]);
        $stmt->bindValue(":registered_app", $cashier["registered_app"]);
        $stmt->bindValue(":allowed_logins", $cashier["allowed_logins"]);
        $stmt->execute();

        return $ctx->db->lastInsertId();
    }

    /**
     * Fetch cashier info by ID.
     * @param object $ctx Context.
     * @param int $id Cashier ID.
     * @return array Cashier info.
     */
    public function getInfoByID($ctx, $id)
    {
        $selectSQL = "SELECT
                `id`,
                `first_name`,
                `last_name`,
                `username`,
                `email`,
                `password`,
                `status`,
                `mobile_number`,
                `created_on`,
                `modified_on`,
                `storeadmin_id`,
                `type_app_admin`,
                `registered_app`,
                `allowed_logins`
            FROM `device_users`
            WHERE `id` = :id";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch cashier info by ID.
     * @param object $ctx Context.
     * @param int $id Cashier ID.
     * @param string $registeredApp Registered app.
     * @return array Cashier info.
     */
    public function getInfoByEmail($ctx, $email, $registeredApp)
    {
        $selectSQL = "SELECT
                `id`,
                `first_name`,
                `last_name`,
                `username`,
                `email`,
                `password`,
                `status`,
                `mobile_number`,
                `created_on`,
                `modified_on`,
                `storeadmin_id`,
                `type_app_admin`,
                `registered_app`,
                `allowed_logins`
            FROM `device_users`
            WHERE `email` = :email
            AND `registered_app` = :registered_app";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":email", $email);
        $stmt->bindValue(":registered_app", $registeredApp);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch all cashiers.
     * @param object $ctx Context.
     * @param string $registeredApp Registered app.
     * @return array Cashiers.
     */
    public function getAll($ctx, $registeredApp)
    {
        $selectSQL = "SELECT
                `id`,
                `first_name`,
                `last_name`,
                `username`,
                `email`,
                `password`,
                `mobile_number`,
                `status`,
                `created_on`,
                `modified_on`,
                `storeadmin_id`,
                `type_app_admin`,
                `registered_app`,
                `allowed_logins`
            FROM `device_users`
            WHERE `registered_app` = :registered_app
            ORDER BY `first_name` ASC";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":registered_app", $registeredApp);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get cashier name.
     * @param object $cashier Cashier info.
     * @return string Name.
     */
    public function getName(&$cashier)
    {
        return $cashier["first_name"] . " " . $cashier["last_name"];
    }

    /**
     * Update cashier.
     * @param object $ctx Context.
     * @param array $cashier Cashier info.
     * @return int Row count.
     */
    public function update($ctx, $cashier)
    {
        $updateSQL = "UPDATE `device_users` SET
                `first_name` = :first_name,
                `last_name` = :last_name,
                `username` = :username,
                `email` = :email,
                `password` = :password,
                `mobile_number` = :mobile_number,
                `status` = :status,
                `created_on` = :created_on,
                `modified_on` = :modified_on,
                `storeadmin_id` = :storeadmin_id,
                `type_app_admin` = :type_app_admin,
                `registered_app` = :registered_app,
                `allowed_logins` = :allowed_logins
            WHERE `id` = :id";
        $stmt = $ctx->db->prepare($updateSQL);
        $stmt->bindValue(":first_name", $cashier["first_name"]);
        $stmt->bindValue(":last_name", $cashier["last_name"]);
        $stmt->bindValue(":username", $cashier["username"]);
        $stmt->bindValue(":email", $cashier["email"]);
        $stmt->bindValue(":password", $cashier["password"]);
        $stmt->bindValue(":mobile_number", $cashier["mobile_number"]);
        $stmt->bindValue(":status", $cashier["status"]);
        $stmt->bindValue(":created_on", $cashier["created_on"]);
        $stmt->bindValue(":modified_on", $cashier["modified_on"]);
        $stmt->bindValue(":storeadmin_id", $cashier["storeadmin_id"]);
        $stmt->bindValue(":type_app_admin", $cashier["type_app_admin"]);
        $stmt->bindValue(":registered_app", $cashier["registered_app"]);
        $stmt->bindValue(":allowed_logins", $cashier["allowed_logins"]);
        $stmt->bindValue(":id", $cashier["id"]);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Update cashier password.
     * @param object $ctx Context.
     * @param string $pwHash Hashed password.
     * @param int $id Cashier ID.
     * @return int Row count.
     */
    public function updatePassword($ctx, $pwHash, $id)
    {
        $updateSQL = "UPDATE `device_users`
            SET `password` = :pwhash
            WHERE `id` = :id";
        $stmt = $ctx->db->prepare($updateSQL);
        $stmt->bindValue(":pwhash", $pwHash);
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
            SELECT * FROM `device_users` WHERE `email` = :email
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
            SELECT * FROM `device_users`
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
     * Check If User exists.
     * @param object $ctx Context.
     * @param int $id Cashier Id.
     * @return bool Exists or not.
     * @throws Exception.
     */
    public function userExists($ctx, $id)
    {
        $selectSQL = "SELECT EXISTS(
            SELECT * FROM `device_users` 
                WHERE `id` = :id 
                AND `status` = 1
        ) AS `user_found`";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":id", $id);
        $stmt->execute();

        return(bool) $stmt->fetchColumn();
    }

    /**
     * Delete user by changing status.
     * @param object $ctx Context.
     * @param int $id Cashier Id.
     * @return bool Status Changed or not.
     * @throws Exception.
     */
    public function statusUpdate($ctx, $id, $status)
    {
        $updateSQL = "UPDATE `device_users` 
                    SET `status` = :status
                    WHERE `id` = :id";
        $stmt = $ctx->db->prepare($updateSQL);
        $stmt->bindValue(":status", $status);
        $stmt->bindValue(":id", $id);
        $stmt->execute();

        return(bool) $stmt->fetchColumn();        
    }
}
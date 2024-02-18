<?php

namespace Plat4mAPI\Model;

use PDO;

class AdminLogin
{
    /**
     * Create a record in admin_login table.
     * @param object $ctx Context.
     * @param int $adminID Admin ID.
     * @param string $registeredApp Registered app name.
     * @return int Last insert ID.
     */
    public function create($ctx, $adminID, $registeredApp)
    {
        $insertSQL = "INSERT INTO `admin_login` (
                `admin_id`,
                `app_name`,
                `app_instance_id`,
                `app_device`,
                `app_version`,
                `app_platform`,
                `user_agent`,
                `created_on`,
                `updated_on`
            ) VALUES (
                :admin_id,
                :app_name,
                :app_instance_id,
                :app_device,
                :app_version,
                :app_platform,
                :user_agent,
                :created_on,
                :updated_on
            )";
        $stmt = $ctx->db->prepare($insertSQL);
        $stmt->bindValue(":admin_id", $adminID);
        $stmt->bindValue(":app_name", $registeredApp);
        $stmt->bindValue(":app_instance_id", $ctx->clientApp->instanceID);
        $stmt->bindValue(":app_device", $ctx->clientApp->device);
        $stmt->bindValue(":app_version", $ctx->clientApp->version);
        $stmt->bindValue(":app_platform", $ctx->clientApp->platform);
        $stmt->bindValue(":user_agent", $ctx->clientApp->userAgent);
        $stmt->bindValue(":created_on", $ctx->now);
        $stmt->bindValue(":updated_on", $ctx->now);
        $stmt->execute();

        return $ctx->db->lastInsertId();
    }

    /**
     * Fetch number of devices admin has active login session.
     * @param object $ctx Context.
     * @param int $adminID Admin ID.
     * @param string $registeredApp Registered app name.
     * @return int Devices count.
     */
    public function loggedInDevicesCount($ctx, $adminID, $registeredApp)
    {
        $selectSQL = "SELECT COUNT(*) FROM `admin_login`
            WHERE `admin_id` = :admin_id
            AND `app_name` = :app_name
            AND `app_instance_id` = :app_instance_id";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":admin_id", $adminID);
        $stmt->bindValue(":app_name", $registeredApp);
        $stmt->bindValue(":app_instance_id", $ctx->clientApp->instanceID);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    /**
     * Fetch instance IDs of devices admin has active login session.
     * @param object $ctx Context.
     * @param int $adminID Admin ID.
     * @param string $registeredApp Registered app name.
     * @return array Instance IDs.
     */
    public function loggedInInstanceIDs($ctx, $adminID, $registeredApp)
    {
        $selectSQL = "SELECT `app_instance_id` FROM `admin_login`
            WHERE `admin_id` = :admin_id
            AND `app_name` = :app_name";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":admin_id", $adminID);
        $stmt->bindValue(":app_name", $registeredApp);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $instanceIDs = [];

        foreach ($rows as $row) {
            $instanceIDs[] = $row["app_instance_id"];
        }

        return $instanceIDs;
    }

    /**
     * Delete admin login record.
     * @param object $ctx Context.
     * @param int $adminID Admin ID.
     * @param string $registeredApp Registered app name.
     * @return int
     */
    public function delete($ctx, $adminID, $registeredApp)
    {
        $deleteSQL = "DELETE FROM `admin_login`
            WHERE `admin_id` = :admin_id
            AND `app_name` = :app_name
            AND `app_instance_id` = :app_instance_id";
        $stmt = $ctx->db->prepare($deleteSQL);
        $stmt->bindValue(":admin_id", $adminID);
        $stmt->bindValue(":app_name", $registeredApp);
        $stmt->bindValue(":app_instance_id", $ctx->clientApp->instanceID);
        $stmt->execute();

        return (int) $stmt->rowCount();
    }

    /**
     * Delete other login records except self.
     * @param object $ctx Context.
     * @param int $adminID Admin ID.
     * @param string $registeredApp Registered app name.
     */
    public function deleteOther($ctx, $adminID, $registeredApp)
    {
        $deleteSQL = "DELETE FROM `admin_login`
            WHERE `admin_id` = :admin_id
            AND `app_name` = :app_name
            AND `app_instance_id` <> :app_instance_id";
        $stmt = $ctx->db->prepare($deleteSQL);
        $stmt->bindValue(":admin_id", $adminID);
        $stmt->bindValue(":app_name", $registeredApp);
        $stmt->bindValue(":app_instance_id", $ctx->clientApp->instanceID);
        $stmt->execute();

        return (int) $stmt->rowCount();
    }
}

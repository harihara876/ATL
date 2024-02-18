<?php

namespace Plat4mAPI\Model;

use PDO;

class CashierLogin
{
    /**
     * Create a record in cashier_login table.
     * @param object $ctx Context.
     * @param int $cashierID Cashier ID.
     * @param string $registeredApp Registered app name.
     * @return int Last insert ID.
     */
    public function create($ctx, $cashierID, $registeredApp)
    {
        $insertSQL = "INSERT INTO `cashier_login` (
                `cashier_id`,
                `app_name`,
                `app_instance_id`,
                `app_device`,
                `app_version`,
                `app_platform`,
                `user_agent`,
                `created_on`,
                `updated_on`
            ) VALUES (
                :cashier_id,
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
        $stmt->bindValue(":cashier_id", $cashierID);
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
     * Fetch number of devices cashier has active login session.
     * @param object $ctx Context.
     * @param int $cashierID Cashier ID.
     * @param string $registeredApp Registered app name.
     * @return int Devices count.
     */
    public function loggedInDevicesCount($ctx, $cashierID, $registeredApp)
    {
        $selectSQL = "SELECT COUNT(*) FROM `cashier_login`
            WHERE `cashier_id` = :cashier_id
            AND `app_name` = :app_name
            AND `app_instance_id` = :app_instance_id";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":cashier_id", $cashierID);
        $stmt->bindValue(":app_name", $registeredApp);
        $stmt->bindValue(":app_instance_id", $ctx->clientApp->instanceID);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    /**
     * Fetch instance IDs of devices cashier has active login session.
     * @param object $ctx Context.
     * @param int $adminID Admin ID.
     * @param string $registeredApp Registered app name.
     * @return array Instance IDs.
     */
    public function loggedInInstanceIDs($ctx, $cashierID, $registeredApp)
    {
        $selectSQL = "SELECT `app_instance_id` FROM `cashier_login`
            WHERE `cashier_id` = :cashier_id
            AND `app_name` = :app_name";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":cashier_id", $cashierID);
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
     * Delete cashier login record.
     * @param object $ctx Context.
     * @param int $cashierID Cashier ID.
     * @param string $registeredApp Registered app name.
     * @return int
     */
    public function delete($ctx, $cashierID, $registeredApp)
    {
        $deleteSQL = "DELETE FROM `cashier_login`
            WHERE `cashier_id` = :cashier_id
            AND `app_name` = :app_name
            AND `app_instance_id` = :app_instance_id";
        $stmt = $ctx->db->prepare($deleteSQL);
        $stmt->bindValue(":cashier_id", $cashierID);
        $stmt->bindValue(":app_name", $registeredApp);
        $stmt->bindValue(":app_instance_id", $ctx->clientApp->instanceID);
        $stmt->execute();

        return (int) $stmt->rowCount();
    }

    /**
     * Delete other login records except self.
     * @param object $ctx Context.
     * @param int $cashierID Cashier ID.
     * @param string $registeredApp Registered app name.
     */
    public function deleteOther($ctx, $cashierID, $registeredApp)
    {
        $deleteSQL = "DELETE FROM `cashier_login`
            WHERE `cashier_id` = :cashier_id
            AND `app_name` = :app_name
            AND `app_instance_id` <> :app_instance_id";
        $stmt = $ctx->db->prepare($deleteSQL);
        $stmt->bindValue(":cashier_id", $cashierID);
        $stmt->bindValue(":app_name", $registeredApp);
        $stmt->bindValue(":app_instance_id", $ctx->clientApp->instanceID);
        $stmt->execute();

        return (int) $stmt->rowCount();
    }
}

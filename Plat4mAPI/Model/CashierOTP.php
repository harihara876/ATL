<?php

namespace Plat4mAPI\Model;

use PDO;

class CashierOTP
{
    /**
     * Create cashier OTP record.
     * @param object $ctx Context.
     * @param array $info OTP info.
     * @return int Last insert ID.
     */
    public function create($ctx, $info)
    {
        $insertSQL = "INSERT INTO `store_cashier_otp` (
                `email`,
                `otp`,
                `created`,
                `registered_app`,
                `event`
            ) VALUES (
                :email,
                :otp,
                :created,
                :registered_app,
                :event
            )";
        $stmt = $ctx->db->prepare($insertSQL);
        $stmt->bindValue(":email", $info["email"]);
        $stmt->bindValue(":otp", $info["otp"]);
        $stmt->bindValue(":created", $info["created"]);
        $stmt->bindValue(":registered_app", $info["registered_app"]);
        $stmt->bindValue(":event", $info["event"]);
        $stmt->execute();

        return $ctx->db->lastInsertId();
    }

    /**
     * Fetch cashier OTP record.
     * @param object $ctx Context.
     * @param string $email Email.
     * @param string $registeredApp Registered app.
     * @param string $event Event.
     * @return array OTP record.
     */
    public function getInfo($ctx, $email, $registeredApp, $event)
    {
        $selectSQL = "SELECT
                `id`,
                `email`,
                `otp`,
                `created`,
                `registered_app`
            FROM `store_cashier_otp`
            WHERE `email` = :email
            AND `registered_app` = :registered_app
            AND `event` = :event
            ORDER BY `id` DESC
            LIMIT 1";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->bindValue(":registered_app", $registeredApp, PDO::PARAM_STR);
        $stmt->bindValue(":event", $event, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Delete OTP.
     * @param int $otpID OTP row ID.
     * @return int Affected rows.
     * @throws Exception
     */
    public function delete($ctx, $id)
    {
        $deleteSQL = "DELETE FROM `store_cashier_otp`
            WHERE `id` = :id";
        $stmt = $ctx->db->prepare($deleteSQL);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
}

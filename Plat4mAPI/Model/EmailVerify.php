<?php

namespace Plat4mAPI\Model;

use PDO;

class EmailVerify
{
    /**
     * Create admin OTP record.
     * @param object $ctx Context.
     * @param array $info OTP info.
     * @return int Last insert ID.
     */
    public function create($ctx, $info)
    {
        $insertSQL = "INSERT INTO `verify_email` (
                `email`,
                `otp`,
                `created_on`,
                `event`
            ) VALUES (
                :email,
                :otp,
                :created,
                :event
            )";
        $stmt = $ctx->db->prepare($insertSQL);
        $stmt->bindValue(":email", $info["email"]);
        $stmt->bindValue(":otp", $info["otp"]);
        $stmt->bindValue(":created", $info["created"]);
        $stmt->bindValue(":event", $info["event"]);
        $stmt->execute();

        return $ctx->db->lastInsertId();
    }

    /**
     * Fetch OTP record.
     * @param object $ctx Context.
     * @param string $email Email.
     * @param string $event Event.
     * @return array OTP record.
     */
    public function getInfo($ctx, $email, $event)
    {
        $selectSQL = "SELECT
                `id`,
                `email`,
                `otp`,
                `created_on`,
                `event`
            FROM `verify_email`
            WHERE `email` = :email
            AND `event` = :event
            ORDER BY `id` DESC
            LIMIT 1";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
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
        $deleteSQL = "DELETE FROM `verify_email`
            WHERE `id` = :id";
        $stmt = $ctx->db->prepare($deleteSQL);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
}

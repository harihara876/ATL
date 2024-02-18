<?php

namespace Plat4m\Core\API;

use Exception;
use PDO;
use PDOException;

class Admin
{
    // DB connection object.
    private $db;

    /**
     * Connects to DB on invoke.
     * @param object $db PDO.
     */
    public function __construct($db)
    {
        if (!$db) {
            throw new Exception("Requires DB connection");
        }

        $this->db = $db;
    }

    /**
     * Fetch admin info by email.
     * @param string $email Email.
     * @return object Admin info.
     * @throws Exception
     */
    public function getInfoByEmail($email)
    {
        try {
            $selectSQL = "SELECT
                    `admin_id` AS `id`,
                    `name`,
                    `email`,
                    `password`,
                    `user_img`,
                    `image_handel` AS `image_handle`,
                    `currency`,
                    `tax`,
                    `shipping`,
                    `store_name` AS `company`,
                    `address`,
                    `phone`,
                    `created_on`,
                    `modified_on`,
                    `type_appstatus`,
                    `status`
                FROM `admin`
                WHERE `email` = :email LIMIT 1";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":email", $email, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetch admin info by ID.
     * @param int $storeAdminID Store admin ID.
     * @return object Admin info.
     * @throws Exception
     */
    public function getInfoByID($storeAdminID)
    {
        try {
            $selectSQL = "SELECT
                    `admin_id` AS `id`,
                    `name`,
                    `email`,
                    `password`,
                    `user_img`,
                    `image_handel` AS `image_handle`,
                    `currency`,
                    `tax`,
                    `shipping`,
                    `store_name` AS `company`,
                    `address`,
                    `phone`,
                    `created_on`,
                    `modified_on`,
                    `type_appstatus`,
                    `status`
                FROM `admin`
                WHERE `admin_id` = :storeAdminID LIMIT 1";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID", $storeAdminID, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Store OTP.
     * @param string $email Email.
     * @param string $otp OTP.
     * @param string $created Timestamp.
     * @return string Last insert ID.
     * @throws Exception
     */
    public function storeOTP($email, $otp, $created)
    {
        try {
            $insertSQL = "INSERT INTO `store_admin_otp` (`email`, `otp`,`created`)
                VALUES (:email, :otp, :created)";
            $stmt = $this->db->prepare($insertSQL);
            $stmt->bindValue(":email", $email, PDO::PARAM_STR);
            $stmt->bindValue(":otp", $otp, PDO::PARAM_STR);
            $stmt->bindValue(":created", $created, PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Get OTP info.
     * @param string $email Email.
     * @param string $otp OTP.
     * @return object OTP info.
     * @throws Exception
     */
    public function getOTPInfo($email, $otp)
    {
        try {
            $selectSQL = "SELECT * FROM `store_admin_otp`
                WHERE `email` = :email AND `otp` = :otp LIMIT 1";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":email", $email, PDO::PARAM_STR);
            $stmt->bindValue(":otp", $otp, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Verify OTP.
     * @param object $otpInfo OTP info.
     * @return boolean Valid or not.
     * @throws Exception
     */
    public function verifyOTP($otpInfo)
    {
        try {
            if (!$otpInfo) {
                return FALSE;
            }

            // If OTP created time is future time, return TRUE.
            if (strtotime($otpInfo->created) > time()) {
                return TRUE;
            }

            // Calculate time difference b/w current time and OTP created time.
            $diffInMinutes = round((time() - strtotime($otpInfo->created)) / 60);

            // If difference is greater than limit, return FALSE.
            if ($diffInMinutes > (60 * 60)) {
                return FALSE;
            }

            return TRUE;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Update password.
     * @param string $email Email.
     * @param string $password Password.
     * @return int Affected rows.
     * @throws Exception
     */
    public function updatePassword($email, $password)
    {
        try {
            $modifiedOn = date("Y-m-d H:i:s");
            $updateSQL = "UPDATE `admin`
                SET `password` = :password, `modified_on` = :modifiedOn
                WHERE `email` = :email";
            $stmt = $this->db->prepare($updateSQL);
            $stmt->bindValue(":email", $email, PDO::PARAM_STR);
            $stmt->bindValue(":password", $password, PDO::PARAM_STR);
            $stmt->bindValue(":modifiedOn", $modifiedOn, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Delete OTP by ID.
     * @param int $otpID OTP row ID.
     * @return int Affected rows.
     * @throws Exception
     */
    public function deleteOTPByID($otpID)
    {
        try {
            $deleteSQL = "DELETE FROM `store_admin_otp`
                WHERE `id` = :otpID";
            $stmt = $this->db->prepare($deleteSQL);
            $stmt->bindValue(":otpID", $otpID, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches device users of a store by store admin ID.
     * @param int $storeAdminID Store admin ID.
     * @return array Device users.
     * @throws Exception
     */
    public function getDeviceUsersByAdminID($storeAdminID)
    {
        try {
            $selectSQL = "SELECT * FROM `device_users`
                WHERE `storeadmin_id` = :storeAdminID";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID", $storeAdminID, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Store Paytm credentials.
     * @param int $storeAdminID Store admin ID.
     * @param string $credentials Credentials.
     * @return int Affected rows count.
     * @throws Exception
     */
    public function storePaytmCredentials($storeAdminID, $credentials)
    {
        try {
            $insertSQL = "UPDATE `admin`
                SET `paytm_credentials` = :credentials
                WHERE admin_id = :storeAdminID";
            $stmt = $this->db->prepare($insertSQL);
            $stmt->bindValue(":credentials", $credentials, PDO::PARAM_STR);
            $stmt->bindValue(":storeAdminID", $storeAdminID, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->rowCount();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), HTTP_STATUS_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Paytm credentials.
     * @param int $storeAdminID Store admin ID.
     * @return string Paytm credentials.
     * @throws Exception
     */
    public function getPaytmCredentials($storeAdminID)
    {
        try {
            $selectSQL = "SELECT `paytm_credentials` FROM `admin`
                WHERE `admin_id` = :storeAdminID LIMIT 1";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID", $storeAdminID, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchColumn();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), HTTP_STATUS_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Returns currency details of Admin.
     * @param int $storeAdminID Store admin ID.
     * @return array Currency details.
     * @throws Exception
     */
    public function getCurrency($storeAdminID)
    {
        try {
            $selectSQL = "SELECT `currency`, `currency_symbol`
                FROM `admin`
                WHERE `admin_id` = :storeAdminID
                LIMIT 1";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID", $storeAdminID, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), HTTP_STATUS_INTERNAL_SERVER_ERROR);
        }
    }
}

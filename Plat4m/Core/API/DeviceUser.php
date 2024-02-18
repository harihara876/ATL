<?php

namespace Plat4m\Core\API;

use Exception;
use PDO;
use PDOException;

class DeviceUser
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
            throw new Exception("Requires DB connection", 500);
        }

        $this->db = $db;
    }

    public function insertDeviceUser($first_name, $last_name, $email, $password, $storeadmin_id, $registeredApp)
    {
        try {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $created_on = date("Y-m-d H:i:s");
            $status = 1;
            $storeadmin = "storeadmin";
            $insertSQL = "INSERT INTO `device_users` (`first_name`, `last_name`, `email`, `password`, `status`, `created_on`, `storeadmin_id`, `type_app_admin`, `registered_app`)
                VALUES (:first_name, :last_name, :email, :password, :status, :created_on, :storeadmin_id, :type_app_admin, :registered_app)";
            $stmt = $this->db->prepare($insertSQL);
            $stmt->bindParam(":first_name", $first_name);
            $stmt->bindParam(":last_name", $last_name);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $password);
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":created_on", $created_on);
            $stmt->bindParam(":storeadmin_id", $storeadmin_id);
            $stmt->bindParam(":type_app_admin", $storeadmin);
            $stmt->bindParam(":registered_app", $registeredApp);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $ex) {
            throw new Exception("Failed to create user");
        }
    }

    public function updateDeviceUser($id, $firstName, $lastName, $email, $password)
    {
        try {
            $modifiedOn = date("Y-m-d H:i:s");

            if ($password) {
                $password = password_hash($password, PASSWORD_DEFAULT);
                $updateSQL = "UPDATE `device_users`
                    SET `first_name` = :firstName,
                        `last_name` = :lastName,
                        `email` = :email,
                        `password` = :password,
                        `modified_on` = :modifiedOn
                    WHERE id = :id";
            } else {
                $updateSQL = "UPDATE `device_users`
                    SET `first_name` = :firstName,
                        `last_name` = :lastName,
                        `email` = :email,
                        `modified_on` = :modifiedOn
                    WHERE id = :id";
            }

            $stmt = $this->db->prepare($updateSQL);
            $stmt->bindValue(":firstName", $firstName);
            $stmt->bindValue(":lastName", $lastName);
            $stmt->bindValue(":email", $email);
            $stmt->bindValue(":modifiedOn", $modifiedOn);
            $stmt->bindValue(":id", $id);

            if ($password) {
                $stmt->bindValue(":password", $password);
            }

            $stmt->execute();

            return $stmt->rowCount();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }


    public function insertAdminUser($name, $email, $password, $registeredApp)
    {
        try {
            $pwHash = password_hash($password, PASSWORD_DEFAULT);
            $created_on = date("Y-m-d H:i:s");
            $status = 1;
            $user_img = "";
            $image_handel = 10;
            $storeadmin = "storeadmin";
            $insertSQL = "INSERT INTO `admin` (
                `name`, `email`, `password`, `pwhash`, `user_img`, `image_handel`, `currency`, `shipping`, `store_name`,
                `address`, `phone`, `status`, `created_on`, `type_appstatus`, `registered_app`)
                VALUES (:name, :email, :password, :pwHash, :user_img, :image_handel, :currency, :shipping, :store_name,
                :address, :phone, :status, :created_on, :type_appstatus, :registered_app)";
            $stmt = $this->db->prepare($insertSQL);
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $password);
            $stmt->bindParam(":pwHash", $pwHash);
            $stmt->bindParam(":user_img", $user_img);
            $stmt->bindParam(":image_handel", $image_handel);
            $stmt->bindValue(":currency", "");
            $stmt->bindValue(":shipping", "");
            $stmt->bindValue(":store_name", "");
            $stmt->bindValue(":address", "");
            $stmt->bindValue(":phone", "");
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":created_on", $created_on);
            $stmt->bindParam(":type_appstatus", $storeadmin);
            $stmt->bindParam(":registered_app", $registeredApp);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $ex) {
            throw new Exception("Failed to create user");
        }
    }

    public function updateAdminUser($id, $name, $email, $password)
    {
        try {
            $modifiedOn = date("Y-m-d H:i:s");

            if ($password) {
                $pwHash = password_hash($password, PASSWORD_DEFAULT);
                // TODO: Hash password.
                $updateSQL = "UPDATE `admin`
                    SET `name` = :name,
                        `email` = :email,
                        `password` = :password,
                        `pwhash` = :pwHash,
                        `modified_on` = :modifiedOn
                    WHERE admin_id = :id";
            } else {
                $updateSQL = "UPDATE `admin`
                    SET `name` = :name,
                        `email` = :email,
                        `modified_on` = :modifiedOn
                    WHERE admin_id = :id";
            }

            $stmt = $this->db->prepare($updateSQL);
            $stmt->bindValue(":name", $name);
            $stmt->bindValue(":email", $email);
            $stmt->bindValue(":modifiedOn", $modifiedOn);
            $stmt->bindValue(":id", $id);

            if ($password) {
                $stmt->bindValue(":password", $password);
                $stmt->bindValue(":pwHash", $pwHash);
            }

            $stmt->execute();

            return $stmt->rowCount();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }



    /**
     * Fetches all device users.
     * @return array Device users.
     */
    public function getAll()
    {
        $selectSQL = "SELECT * FROM `device_users` ORDER BY `first_name` ASC";
        $stmt = $this->db->prepare($selectSQL);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    /**
     * Fetches device user info by ID.
     * @param int $id Device user ID.
     * @return mixed Array of user details on success, FALSE on failure.
     */
    public function getInfoByID($id)
    {
        $selectSQL = "SELECT *
            FROM `device_users`
            WHERE `id` = :id
            LIMIT 1";
        $stmt = $this->db->prepare($selectSQL);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }



    /**
     * Fetches device user info by ID.
     * @param int $id Device user ID.
     * @return mixed Array of user details on success, FALSE on failure.
     */
    public function getAdminInfoByID($id)
    {
        $selectSQL = "SELECT *
            FROM `admin`
            WHERE `admin_id` = :id
            LIMIT 1";
        $stmt = $this->db->prepare($selectSQL);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }



    /**
     * Fetches device user info by email.
     * @param string $email Device user email.
     * @return mixed Array of user details on success, FALSE on failure.
     */
    public function getInfoByEmail($email)
    {
        $selectSQL = "SELECT *
            FROM `device_users`
            WHERE `email` = :email
            LIMIT 1";
        $stmt = $this->db->prepare($selectSQL);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAdminInfoByEmail($email)
    {
        $selectSQL = "SELECT *
            FROM `admin`
            WHERE `email` = :email
            LIMIT 1";
        $stmt = $this->db->prepare($selectSQL);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getInfoByEmailPass($email, $password)
    {
        $selectSQL = "SELECT *
            FROM `device_users`
            WHERE `email` = :email and `password`=:password
            LIMIT 1";
        $stmt = $this->db->prepare($selectSQL);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $password);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch number of cashiers associated to an admin.
     * @param object $ctx Context.
     * @param int $adminID Admin ID.
     * @return int
     */
    public function getCashiersCount($ctx, $adminID)
    {
        $selectSQL = "SELECT COUNT(*) FROM `device_users` WHERE `storeadmin_id` = :adminID";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindParam(":adminID", $adminID);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }
}

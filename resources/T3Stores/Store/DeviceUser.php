<?php

namespace T3Stores\Store;

use \PDO;
use PDOException;
use Exception;

class DeviceUser
{
    // DB connection object.
    private $db;



    /**
     * Connects to DB on invoke.
     */
    public function __construct($db)
    {
        if (!$db) {
            throw new Exception("Requires DB connection");
        }

        $this->db = $db;
    }


    public function insertDeviceUser($first_name, $last_name, $email, $password, $storeadmin_id)
    {
        try {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $created_on = date("Y-m-d H:i:s");
            $status = 1;
            $storeadmin = "storeadmin";
            $insertSQL = "INSERT INTO `device_users` (`first_name`, `last_name`, `email`, `password`, `status`, `created_on`, `storeadmin_id`, `type_app_admin`)
                VALUES (:first_name, :last_name, :email, :password, :status, :created_on, :storeadmin_id, :type_app_admin)";
            $stmt = $this->db->prepare($insertSQL);
            $stmt->bindParam(":first_name", $first_name);
            $stmt->bindParam(":last_name", $last_name);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $password);
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":created_on", $created_on);
            $stmt->bindParam(":storeadmin_id", $storeadmin_id);
            $stmt->bindParam(":type_app_admin", $storeadmin);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $ex) {
            throw new Exception("Failed to create user");
        }
    }

    public function updateDeviceUser($id, $first_name, $last_name, $email, $password)
    {
        try {
            $modified_on = date("Y-m-d H:i:s");
            if ($password) {
                $password = password_hash($password, PASSWORD_DEFAULT);
                $updateSQL = "UPDATE `device_users` SET `first_name` = '$first_name', `last_name` = '$last_name', `email` = '$email', `password` = '$password', `modified_on` = '$modified_on' WHERE id = $id";
            } else {
                $updateSQL = "UPDATE `device_users` SET `first_name` = '$first_name', `last_name` = '$last_name', `email` = '$email', `modified_on` = '$modified_on' WHERE id = $id";
            }

            var_dump($updateSQL);
            die;
            $stmt = $this->db->prepare($updateSQL);
            $stmt->execute();
            return true;
        } catch (PDOException $ex) {
            throw new Exception("Failed to update user");
        }
    }


    public function insertAdminUser($name, $email, $password)
    {
        try {
            $created_on = date("Y-m-d H:i:s");
            $status = 1;
            $user_img = "https://www.smashusmle.com/wp-content/uploads/2015/01/User-icon.png";
            $image_handel = 10;
            $storeadmin = "storeadmin";
            $insertSQL = "INSERT INTO `admin` (`name`, `email`, `password`, `user_img`,  `image_handel`,  `status`, `created_on`, `type_appstatus`)
                VALUES (:name, :email, :password, :user_img, :image_handel, :status, :created_on, :type_appstatus)";
            $stmt = $this->db->prepare($insertSQL);
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $password);
            $stmt->bindParam(":user_img", $user_img);
            $stmt->bindParam(":image_handel", $image_handel);
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":created_on", $created_on);
            $stmt->bindParam(":type_appstatus", $storeadmin);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $ex) {
            throw new Exception("Failed to create user");
        }
    }

    public function updateAdminUser($id, $name, $email, $password)
    {
        try {
            $modified_on = date("Y-m-d H:i:s");
            if ($password) {
                $updateSQL = "UPDATE `admin` SET `name` = '$name', `email` = '$email', `password` = '$password', `modified_on` = '$modified_on' WHERE admin_id = $id";
            } else {
                $updateSQL = "UPDATE `admin` SET `name` = '$name', `email` = '$email', `modified_on` = '$modified_on' WHERE admin_id = $id";
            }
            $stmt = $this->db->prepare($updateSQL);
            $stmt->execute();
            return true;
        } catch (PDOException $ex) {
            throw new Exception("Failed to update user");
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
}

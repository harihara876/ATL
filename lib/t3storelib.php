<?php

class t3storeLib
{
    public function adminLogin($db, $email, $password)
    {
        $selectSQL = "SELECT * FROM `admin` WHERE `email` = :email LIMIT 1";
        $stmt = $db->prepare($selectSQL);
        $stmt->bindValue(":email", $email);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin) {
            return FALSE;
        }

        if (!password_verify($password, $admin["pwhash"])) {
            return FALSE;
        }

        return $admin;
    }

    /*
     * Login User
     *
     * @param $user_email, $user_pwd
     * @return data
     * */
    public function Login($user_email, $user_pwd)
    {
        $conn = db();
        $query = mysqli_query($conn, "SELECT * FROM admin WHERE email='" . $user_email . "' AND password='" . $user_pwd . "' AND  ( `type_appstatus`='ADMIN' OR `type_appstatus`='storeadmin') ");

        if (mysqli_num_rows($query) > 0 && mysqli_num_rows($query) == 1) {
            return $query->fetch_array(MYSQLI_ASSOC);
            //mysqli_fetch_all($result,MYSQLI_ASSOC);
            //return $query;
        } else {
            return false;
        }
    }


    /*
     * Add Logged User
     *
     * @param $uid
     * @return data
     * */
    public function Logs($uid)
    {
        $conn = db();
        $sql = "INSERT INTO logs(uid)VALUES ('" . $uid . "')";
        $query = mysqli_query($conn, $sql);
        return true;
    }

    /*
     * Get Logged Users
     *
     * @param $uid, $type
     * @return data
     * */
    public function getLogs($user_id, $user_type)
    {
        $conn = db();
        if ($user_type == "ADMIN") {
            $sql = "SELECT id,`name`,`email`,`type_appstatus`,`phone`,`timestap`
				FROM `logs` l
				LEFT JOIN `admin` u ON u.`admin_id`=l.uid
				WHERE `status`=1 ORDER BY `timestap` DESC";
        } else {
            $sql = "SELECT id,`name`,`email`,`type_appstatus`,`phone`,`timestap`
				FROM `logs` l
				LEFT JOIN `admin` u ON u.`admin_id`=l.uid
				WHERE `uid`='" . $user_id . "' and `status`=1 ORDER BY `timestap` DESC";
        }
        $query = mysqli_query($conn, $sql);
        if (mysqli_num_rows($query) > 0) {
            //return mysqli_fetch_all($query,MYSQLI_ASSOC);
            //$data = [];
            while ($data[] = $query->fetch_assoc()) {
            }
            return $data;
        } else {
            return false;
        }
    }


    /*
     * Get All Products
     *
     * @param $user_type
     * @return data
     * */
    public function getProducts($user_id, $user_type)
    {
        $conn = db();
        if ($user_type == "ADMIN") {
            $sql = "SELECT p.product_id,p.id,p.product_name,p.UPC,p.quantity,p.Regular_Price,p.sellprice,img.image FROM `app_productsmain` p
				LEFT JOIN product_images img ON img.product_id=p.product_id
				ORDER BY p.id DESC";
        } else {
            $sql = "SELECT p.product_id,p.id,p.product_name,p.UPC,p.quantity,p.Regular_Price,price.sellprice,img.image FROM `appuser_productsmain` p
				LEFT JOIN product_images img ON img.product_id=p.product_id
				LEFT JOIN app_product_price_change price ON price.product_id=p.product_id
		        where `storeadmin_id`='" . $user_id . "' and  `type_app_admin`='" . $user_type . "' ORDER BY p.id DESC";
        }
        $query = mysqli_query($conn, $sql);
        if (mysqli_num_rows($query) > 0) {
            //return mysqli_fetch_all($query,MYSQLI_ASSOC);
            $data = [];
            while ($data[] = $query->fetch_assoc()) {
            }
            return $data;
        } else {
            return false;
        }
    }



    /*
     * Get All Categories
     *
     * @param $user_type
     * @return data
     * */
    public function getCategories($user_id, $user_type)
    {
        $conn = db();
        if ($user_type == "ADMIN") {
            $sql = "SELECT * FROM `category` order by cat_id DESC ";
        } else {
            $sql = "SELECT  c.`cat_id`,`category_name`,`category_image`, c.Description
				FROM `category` c
				INNER JOIN products p ON p.`Category_Id`=c.`cat_id`
				INNER JOIN `product_details` pd ON pd.`product_id`=p.product_id
				WHERE pd.`storeadmin_id`='" . $user_id . "'  GROUP BY c.`cat_id`  ORDER BY c.`cat_id`";
        }
        $query = mysqli_query($conn, $sql);
        if (mysqli_num_rows($query) > 0) {
            //return mysqli_fetch_all($query,MYSQLI_ASSOC);
            $data = [];
            while ($data[] = $query->fetch_assoc()) {
            }
            return $data;
        } else {
            return false;
        }
    }



    /*
     * Get All SubCategories
     *
     * @param $user_type
     * @return data
     * */
    public function getSubCategories($user_id, $user_type)
    {
        // var_dump($user_id, $user_type);
        $conn = db();
        if ($user_type == "ADMIN") {
            $sql = "SELECT  c.`cat_id`,`category_name`,`category_image`,Sub_Category_Id,`Sub_Category_Name`,Image as Image
				FROM `category` c
				INNER JOIN `subcategories` sc ON sc.`cat_id`=c.`cat_id`
				GROUP BY Sub_Category_Id   ORDER BY c.`cat_id`";
        } else {
            $sql = "SELECT  c.`cat_id`,`category_name`,`category_image`,Sub_Category_Id,`Sub_Category_Name`,sc.Image AS Image, c.Description
				FROM `category` c
				INNER JOIN products p ON p.`Category_Id`=c.`cat_id`
				INNER JOIN `product_details` pd ON pd.`product_id`=p.product_id
				INNER JOIN `subcategories` sc ON sc.`cat_id`=c.`cat_id`
				WHERE pd.`storeadmin_id`='" . $user_id . "' GROUP BY c.`cat_id` ORDER BY `Sub_Category_Id` ";
        }
        $query = mysqli_query($conn, $sql);
        if (!$query) {
            return false;
        }
        if (mysqli_num_rows($query) > 0) {
            //return mysqli_fetch_all($query,MYSQLI_ASSOC);
            $data = [];
            while ($data[] = $query->fetch_assoc()) {
            }
            return $data;
        } else {
            return false;
        }
    }


    public function getOrderReports($user_id, $user_type, $dt, $dt1, $storeid)
    {
        $conn = db();
        if ($user_type == "ADMIN") {
            if ($storeid) {
                $store_sql = " AND `device_users`.`storeadmin_id`='$storeid'";
            }
            $sql = "SELECT `device_users`.`id`, device_users.`first_name`, `device_users`.`storeadmin_id`,`users_orders`.`id`, `users_orders`.`order_id`, `users_orders`.`paymentref`, `users_orders`.`paymentmode`, `users_orders`.`payment_status`, `users_orders`.`address`, `users_orders`.`order_date`,
				`users_orders`.`order_status`, `users_orders`.`total`, `users_orders`.`uid`,admin.`name` AS admin_name
				FROM `device_users`
				JOIN `users_orders`
				JOIN `admin` ON admin.`admin_id`=`device_users`.`storeadmin_id`
				WHERE `device_users`.`id` = `users_orders`.`uid`  AND DATE(order_date) BETWEEN '$dt' AND '$dt1' " . $store_sql;
        } else {
            $storeid = $_SESSION['id'];
            $store_sql = " AND `device_users`.`storeadmin_id`='$storeid'";
            $sql = "SELECT `device_users`.`id`, device_users.`first_name`, `device_users`.`storeadmin_id`,`users_orders`.`id`, `users_orders`.`order_id`, `users_orders`.`paymentref`, `users_orders`.`paymentmode`, `users_orders`.`payment_status`, `users_orders`.`address`, `users_orders`.`order_date`,
				`users_orders`.`order_status`, `users_orders`.`total`, `users_orders`.`uid`,admin.`name` AS admin_name
				FROM `device_users`
				JOIN `users_orders`
				JOIN `admin` ON admin.`admin_id`=`device_users`.`storeadmin_id`
				WHERE `device_users`.`id` = `users_orders`.`uid`  AND DATE(order_date) BETWEEN '$dt' AND '$dt1' " . $store_sql;
        }
        $query = mysqli_query($conn, $sql);
        if (mysqli_num_rows($query) > 0) {
            $data = [];
            while ($data[] = $query->fetch_assoc()) {
            }
            return $data;
            //return mysqli_fetch_all($query,MYSQLI_ASSOC);
        } else {
            return false;
        }
    }


    public function getProductReports($user_id, $user_type, $dt, $dt1, $storeid)
    {
        $conn = db();
        if ($user_type == "ADMIN") {
            if ($storeid) {
                $store_sql = " AND `pd`.`storeadmin_id`='$storeid'";
            }
            $sql = "SELECT p.product_id,p.id,p.product_name,p.UPC,pd.quantity,pd.Regular_Price,pd.sellprice,img.image,pd.Date_Created,admin.`name`
				FROM `products` p
				LEFT JOIN product_details pd ON pd.product_id = p.product_id
				LEFT JOIN product_images img ON img.product_id=p.product_id
				LEFT JOIN admin ON admin.admin_id=pd.`storeadmin_id`
				WHERE DATE(pd.Date_Created) BETWEEN '$dt' AND '$dt1' " . $store_sql;
        } else {
            $storeid = $_SESSION['id'];
            $store_sql = " AND `pd`.`storeadmin_id`='$storeid'";
            $sql = "SELECT p.product_id,p.id,p.product_name,p.UPC,pd.quantity,pd.Regular_Price,pd.sellprice,img.image,pd.Date_Created,admin.`name`
				FROM `products` p
				LEFT JOIN product_details pd ON pd.product_id = p.product_id
				LEFT JOIN product_images img ON img.product_id=p.product_id
				LEFT JOIN admin ON admin.admin_id=pd.`storeadmin_id`
				WHERE DATE(pd.Date_Created) BETWEEN '$dt' AND '$dt1' " . $store_sql;
        }
        $query = mysqli_query($conn, $sql);
        if (mysqli_num_rows($query) > 0) {
            $data = [];
            while ($data[] = $query->fetch_assoc()) {
            }
            return $data;
            //return mysqli_fetch_all($query,MYSQLI_ASSOC);
        } else {
            return false;
        }
    }

    public function getUsers()
    {
        $conn = db();
        $sql = "SELECT `admin_id`, `name` FROM `admin` WHERE type_appstatus='storeadmin' ORDER BY `name` ASC";
        $query = mysqli_query($conn, $sql);
        if (mysqli_num_rows($query) > 0) {
            $data = [];
            while ($data[] = $query->fetch_assoc()) {
            }
            return $data;
            //return mysqli_fetch_all($query,MYSQLI_ASSOC);
        } else {
            return false;
        }
    }


    public function getUserReports($user_id, $user_type, $dt, $dt1, $storeid)
    {
        $conn = db();
        if ($user_type == "ADMIN") {

            $sql = "SELECT * FROM (
				SELECT `admin_id` AS id,`name`,`email`,'StoreAdmin' AS type,`created_on` FROM `admin` WHERE `created_on` BETWEEN '$dt' AND '$dt1'
				UNION
				SELECT `id`,`first_name` AS `name`,`email`,'DeviceUser' AS type,`created_on` FROM `device_users` WHERE DATE(created_on) BETWEEN '$dt' AND '$dt1'
				)AS i ORDER BY created_on ASC";
        } else {
        }
        $query = mysqli_query($conn, $sql);
        if (mysqli_num_rows($query) > 0) {
            $data = [];
            while ($data[] = $query->fetch_assoc()) {
            }
            return $data;
            //return mysqli_fetch_all($query,MYSQLI_ASSOC);
        } else {
            return false;
        }
    }
}

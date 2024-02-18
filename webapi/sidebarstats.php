<?php

require_once("../config.php");

/**
 * Fetch admins count.
 * @param object $db DB connection.
 * @return int Admins count.
 */
function getAdminsCount($db)
{
    $selectSQL = "SELECT COUNT(*) AS `count` FROM `admin`";
    $stmt = $db->prepare($selectSQL);
    $stmt->execute();
    return $stmt->fetchColumn();
}

/**
 * Fetch count of users belong to a store.
 * @param object $db DB connection.
 * @param int $adminID Admin ID.
 * @return int Users count.
 */
function getUsersCount($db, $adminID)
{
    $selectSQL = "SELECT COUNT(*) AS `count`
        FROM `device_users`
        WHERE `storeadmin_id` = :adminID";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":adminID", $adminID);
    $stmt->execute();
    return $stmt->fetchColumn();
}

/**
 * Fetch all categories count.
 * @param object $db DB connection.
 * @return int Categories count.
 */
function getCategoriesCount($db)
{
    $selectSQL = "SELECT COUNT(*) AS `count` FROM `category`";
    $stmt = $db->prepare($selectSQL);
    $stmt->execute();
    return $stmt->fetchColumn();
}

/**
 * Fetch all subcategories count.
 * @param object $db DB connection.
 * @return int Subcategories count.
 */
function getSubcategoriesCount($db)
{
    $selectSQL = "SELECT COUNT(*) AS `count` FROM `subcategories`";
    $stmt = $db->prepare($selectSQL);
    $stmt->execute();
    return $stmt->fetchColumn();
}

/**
 * Fetch all sub-subcategories count.
 * @param object $db DB connection.
 * @return int Sub-subcategories count.
 */
function getSubSubcategoriesCount($db)
{
    $selectSQL = "SELECT COUNT(*) AS `count` FROM `subsubcategories`";
    $stmt = $db->prepare($selectSQL);
    $stmt->execute();
    return $stmt->fetchColumn();
}

/**
 * Fetch count of categories belong to a store.
 * @param object $db DB connection.
 * @param int $adminID Admin ID.
 * @return int Categories count.
 */
function getStoreCategoriesCount($db, $adminID)
{
    $selectSQL = "SELECT COUNT(*) AS `count` FROM (
            SELECT `c`.`cat_id`
            FROM `category` `c`
            INNER JOIN `products` `p` ON `p`.`Category_Id` = `c`.`cat_id`
            INNER JOIN `product_details` `pd` ON `pd`.`product_id` = `p`.`product_id`
            WHERE `pd`.`storeadmin_id` = :adminID
            GROUP BY `c`.`cat_id`
        ) `categories`";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":adminID", $adminID);
    $stmt->execute();
    return $stmt->fetchColumn();
}

/**
 * Fetch count of subcategories belong to a store.
 * @param object $db DB connection.
 * @param int $adminID Admin ID.
 * @return int Subcategories count.
 */
function getStoreSubcategoriesCount($db, $adminID)
{
    $selectSQL = "SELECT COUNT(*) AS `count` FROM (
            SELECT `c`.`cat_id`
            FROM `category` `c`
            INNER JOIN `products` `p` ON `p`.`Category_Id` = `c`.`cat_id`
            INNER JOIN `product_details` `pd` ON `pd`.`product_id` = `p`.`product_id`
            INNER JOIN `subcategories` `sc` ON `sc`.`cat_id` = `c`.`cat_id`
            WHERE `pd`.`storeadmin_id` = :adminID
            GROUP BY `c`.`cat_id`
        ) `subcategories`";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":adminID", $adminID);
    $stmt->execute();
    return $stmt->fetchColumn();
}

/**
 * Fetch all products count.
 * @param object $db DB connection.
 * @return int Products count.
 */
function getProductsCount($db)
{
    $selectSQL = "SELECT COUNT(*) AS `count` FROM `products`";
    $stmt = $db->prepare($selectSQL);
    $stmt->execute();
    return $stmt->fetchColumn();
}

/**
 * Fetch count of products belong to a store.
 * @param object $db DB connection.
 * @param int $adminID Admin ID.
 * @return int Products count.
 */
function getStoreProductsCount($db, $adminID)
{
    $selectSQL = "SELECT COUNT(*) AS `count`
        FROM `product_details`
        WHERE `storeadmin_id` = :adminID";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":adminID", $adminID);
    $stmt->execute();
    return $stmt->fetchColumn();
}

/**
 * Fetch all orders count.
 * @param object $db DB connection.
 * @return int Count.
 */
function getOrdersCount($db)
{
    $selectSQL = "SELECT COUNT(*) AS `count` FROM `users_orders`";
    $stmt = $db->prepare($selectSQL);
    $stmt->execute();
    return $stmt->fetchColumn();
}

/**
 * Fetch all new products count.
 * @param object $db DB connection.
 * @return int New products count.
 */
function getNewProductsCount($db)
{
    $selectSQL = "SELECT COUNT(*) AS `count`
        FROM `products_temp`
        WHERE `upc_status_request` = '1'";
    $stmt = $db->prepare($selectSQL);
    $stmt->execute();
    return $stmt->fetchColumn();
}

/**
 * Fetch count of new products belong to a store.
 * @param object $db DB connection.
 * @return int New products count.
 */
function getStoreNewProductsCount($db)
{
    $selectSQL = "SELECT COUNT(*) AS `count`
        FROM `products_temp`
        WHERE `upc_status_request` = '0'";
    $stmt = $db->prepare($selectSQL);
    $stmt->execute();
    return $stmt->fetchColumn();
}

/**
 * Fetch count of orders belong to a store.
 * @param object $db DB connection.
 * @param int $adminID Admin ID.
 * @return int Orders count.
 */
function getStoreOrdersCount($db, $adminID)
{
    $selectSQL = "SELECT COUNT(*) AS `count`
        FROM `users_orders`
        WHERE `uid` IN (
            SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :adminID1
            UNION
            SELECT :adminID2
        )";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":adminID1", $adminID);
    $stmt->bindValue(":adminID2", $adminID);
    $stmt->execute();
    return $stmt->fetchColumn();
}

$db = getDBConn();
$subSubcategoriesCount = getSubSubcategoriesCount($db);

if ($_SESSION["type_app"]  == "ADMIN") {
    $usersCount             = getAdminsCount($db);
    $categoriesCount        = getCategoriesCount($db);
    $subcategoriesCount     = getSubcategoriesCount($db);
    $productsCount          = getProductsCount($db);
    $ordersCount            = getOrdersCount($db);
    $newProductsCount       = getNewProductsCount($db);
} else {
    $usersCount             = getUsersCount($db, $_SESSION["id"]);
    $categoriesCount        = getStoreCategoriesCount($db, $_SESSION["id"]);
    $subcategoriesCount     = getStoreSubcategoriesCount($db, $_SESSION["id"]);
    $productsCount          = getStoreProductsCount($db, $_SESSION["id"]);
    $ordersCount            = getStoreOrdersCount($db, $_SESSION["id"]);
    $newProductsCount       = getStoreNewProductsCount($db);
}

echo json_encode([
    "error" => FALSE,
    "data" => [
        "usersCount"            => $usersCount,
        "categoriesCount"       => $categoriesCount,
        "subcategoriesCount"    => $subcategoriesCount,
        "subSubcategoriesCount" => $subSubcategoriesCount,
        "productsCount"         => $productsCount,
        "ordersCount"           => $ordersCount,
        "newProductsCount"      => $newProductsCount,
    ]
]);
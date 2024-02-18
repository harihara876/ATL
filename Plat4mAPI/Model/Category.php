<?php

namespace Plat4mAPI\Model;

use PDO;

class Category
{
    /**
     * Format category info.
     * @param array $category Category info.
     * @return array Formatted info.
     */
    public function format(&$category)
    {
        if (!$category) {
            return NULL;
        }

        return [
            "id"            => (int) $category["id"],
            "name"          => (string) $category["name"],
            "image_url"     => (string) $category["image_url"],
            "description"   => (string) $category["description"],
            "created_on"    => (string) $category["created_on"],
            "updated_on"    => (string) $category["updated_on"]
        ];
    }

    /**
     * Format multiple categories info.
     * @param array $categories Categories info.
     * @return array Formatted categories.
     */
    public function formatMultiple(&$categories)
    {
        $formattedCategories = [];

        foreach ($categories as $category) {
            $formattedCategories[] = $this->format($category);
        }

        return $formattedCategories;
    }

    /**
     * Fetch all categories in alphabetical order.
     * @param object $ctx Context.
     * @return array List of categories.
     */
    public function getAll($ctx)
    {
        $selectSQL = "SELECT
                `cat_id` AS `id`,
                `category_name` AS `name`,
                `category_image` AS `image_url`,
                `Description` AS `description`,
                `created_on`,
                `updated_on`
            FROM `category`
            ORDER BY `name` ASC";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->execute();
        $rows =  $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->formatMultiple($rows);
    }

    /**
     * Fetches all categories by admin ID.
     * @param object $ctx Context.
     * @param int $adminID Admin ID.
     * @return array Categories.
     */
    public function getAllByAdminID($ctx, $adminID)
    {
        // TODO: Query is too costly. Optimize it.
        $selectSQL = "SELECT
                `category`.`cat_id` AS `id`,
                `category`.`category_name` AS `name`,
                `category`.`category_image` AS `image_url`,
                `category`.`Description` AS `description`,
                `category`.`created_on`,
                `category`.`updated_on`
            FROM `category`
            INNER JOIN `products` ON `products`.`Category_Id` = `category`.`cat_id`
            INNER JOIN `product_details` ON `product_details`.`product_id` = `products`.`product_id`
            WHERE `product_details`.`storeadmin_id` = :adminID
            GROUP BY `category`.`cat_id`
            ORDER BY `category`.`cat_id`";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":adminID", $adminID, PDO::PARAM_INT);
        $stmt->execute();
        $rows =  $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->formatMultiple($rows);
    }

    /**
     * Fetches category info by ID.
     * @param object $ctx Context.
     * @param int $catID Category ID.
     * @return array Category info.
     */
    public function getInfoByID($ctx, $catID)
    {
        $selectSQL = "SELECT
                `cat_id` AS `id`,
                `category_name` AS `name`,
                `category_image` AS `image_url`,
                `Description` AS `description`,
                `created_on`,
                `updated_on`
            FROM `category`
            WHERE `cat_id` = :catID";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":catID", $catID, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $this->format($row);
    }

    /**
     * Select category info by Like %category_name.
     * @param object $ctx Context.
     * @param string $string Category name.
     * @return array Category info.
     */
    public function getInfoByString($ctx, $string)
    {
        $selectSQL = "SELECT 
                `cat_id` AS `id`,
                `category_name` AS `name`,
                `category_image` AS `image_url`,
                `Description` AS `description`,
                `created_on`,
                `updated_on`
            FROM `category`
            WHERE `category_name` = :string";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":string", $string);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $this->format($row);
    }

    /**
     * Create Category.
     * @param object $ctx Context.
     * @param string $category_name category.
     * @return int Last insert ID.
     */
    public function create($ctx, $info)
    {
        $insertSQL = "INSERT INTO `category` (
                `category_name`
            ) VALUES (
                :category_name
            )";
        $stmt = $ctx->db->prepare($insertSQL);
        $stmt->bindValue(":category_name", $info);
        $stmt->execute();

        return $ctx->db->lastInsertId();
    }

     /**
     * Fetches my store category for new users.
     * @param object $ctx Context.
     * @param int $catID Category ID.
     * @return array Categories.
     */
    public function getMyStoreCat($ctx,$catId)
    {
        // TODO: Query is too costly. Optimize it.
        $selectSQL = "SELECT
                `category`.`cat_id` AS `id`,
                `category`.`category_name` AS `name`,
                `category`.`category_image` AS `image_url`,
                `category`.`Description` AS `description`,
                `category`.`created_on`,
                `category`.`updated_on`
            FROM `category`
            WHERE `category`.`cat_id` = :cat_id";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":cat_id", $catId, PDO::PARAM_INT);
        $stmt->execute();
        $rows =  $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->formatMultiple($rows);
    }
}

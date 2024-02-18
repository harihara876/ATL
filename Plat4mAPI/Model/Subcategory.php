<?php

namespace Plat4mAPI\Model;

use PDO;

class Subcategory
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
            "category_id"   => (int) $category["category_id"],
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
     * Fetch all subcategories.
     * @param object $ctx Context.
     * @return array Subcategories.
     */
    public function getAll($ctx)
    {
        // TODO: Optimize query.
        $selectSQL = "SELECT
                `subcategories`.`Sub_Category_Id` AS `id`,
                `subcategories`.`Sub_Category_Name` AS `name`,
                `subcategories`.`Image` AS `image_url`,
                `subcategories`.`Description` AS `description`,
                `category`.`cat_id` AS `category_id`,
                `subcategories`.`created_on`,
                `subcategories`.`updated_on`
            FROM `subcategories`
            JOIN `category` ON `subcategories`.`cat_id` = `category`.`cat_id`";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->execute();
        $rows =  $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->formatMultiple($rows);
    }

    /**
     * Fetch all subcategories by admin ID.
     * @param object $ctx Context.
     * @param int $adminID Admin ID.
     * @return array Subcategories.
     */
    public function getAllByAdminID($ctx, $adminID)
    {
        $selectSQL = "SELECT
                `sc`.`Sub_Category_Id` AS `id`,
                `sc`.`Sub_Category_Name` AS `name`,
                `sc`.`Image` AS `image_url`,
                `sc`.`Description` AS `description`,
                `c`.`cat_id` AS `category_id`,
                `sc`.`created_on`,
                `sc`.`updated_on`
            FROM `subcategories` `sc`
            JOIN `category` `c` ON `sc`.`cat_id` = `c`.`cat_id`
            WHERE `sc`.`Sub_Category_Id` IN (
                SELECT DISTINCT(`p`.`Category_Type`)
                FROM `products` `p` JOIN `product_details` `pd`
                ON `p`.`product_id` = `pd`.`product_id`
                WHERE `pd`.`storeadmin_id` = :adminID
            )";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":adminID", $adminID, PDO::PARAM_INT);
        $stmt->execute();
        $rows =  $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->formatMultiple($rows);
    }

    /**
     * Select Sub_category info by Sub_Category_Name.
     * @param object $ctx Context.
     * @param string $string Sub_Category_Name.
     * @return array Sub_Category info.
     */
    public function getInfoByString($ctx, $string)
    {
        $selectSQL = "SELECT 
                `subcategories`.`Sub_Category_Id` AS `id`,
                `subcategories`.`Sub_Category_Name` AS `name`,
                `subcategories`.`Image` AS `image_url`,
                `subcategories`.`Description` AS `description`,
                `subcategories`.`cat_id` AS `category_id`,
                `subcategories`.`created_on`,
                `subcategories`.`updated_on`
            FROM `subcategories`
            WHERE `Sub_Category_Name` = :string";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":string", $string);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $this->format($row);
    }

    /**
     * Create Sub_Category.
     * @param object $ctx Context.
     * @param array $data sub_cat_name,cat_id.
     * @return int Last insert ID.
     */
    public function create($ctx, $data)
    {
        $insertSQL = "INSERT INTO `subcategories` (
                `Sub_Category_Name`,
                `cat_id`
            ) VALUES (
                :Sub_Category_Name,
                :category_id
            )";
        $stmt = $ctx->db->prepare($insertSQL);
        $stmt->bindValue(":Sub_Category_Name", $data["subcategory"]);
        $stmt->bindValue(":category_id", $data["cat_id"]);
        $stmt->execute();

        return $ctx->db->lastInsertId();
    }

    /**
     * Fetch subcategory info by ID.
     * @param object $ctx Context.
     * @param int $subcatID Subcategory ID.
     * @return array Subcategory info.
     * @throws Exception
     */
    public function getInfoByID($ctx, $subcatID)
    {
        $selectSQL = "SELECT
                `Sub_Category_Id` AS `id`,
                `Sub_Category_Name` AS `name`,
                `Image` AS `image_url`,
                `Description` AS `description`,
                `cat_id` AS `category_id`,
                `created_on`,
                `updated_on`
            FROM `subcategories`
            WHERE `Sub_Category_Id` = :subcatID";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":subcatID", $subcatID, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $this->format($row);
    }
}

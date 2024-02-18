<?php

namespace Plat4mAPI\Model;

use PDO;

class SubSubcategory
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
            "id"                => (int) $category["id"],
            "name"              => (string) $category["name"],
            "image_url"         => (string) $category["image_url"],
            "description"       => (string) $category["description"],
            "subcategory_id"    => (int) $category["subcategory_id"],
            "created_on"        => (string) $category["created_on"],
            "updated_on"        => (string) $category["updated_on"]
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
     * Fetch all sub subcategories.
     * @param object $ctx Context.
     * @return array Sub subcategories.
     */
    public function getAll($ctx)
    {
        $selectSQL = "SELECT
                `Sub_Sub_Category_Id` AS `id`,
                `Sub_Sub_Category_Name` AS `name`,
                `Image` AS `image_url`,
                `Description` AS `description`,
                `Sub_Category_Id` AS `subcategory_id`,
                `created_on`,
                `updated_on`
            FROM `subsubcategories`";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->execute();
        $rows =  $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->formatMultiple($rows);
    }

    /**
     * Fetch all sub-subcategories by admin ID.
     * @param object $ctx Context.
     * @param int $adminID Admin ID.
     * @return array Sub subcategories.
     * @throws Exception
     */
    public function getAllByAdminID($ctx, $adminID)
    {
        return $this->getAll($ctx);
    }
}

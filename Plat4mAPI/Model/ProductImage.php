<?php

namespace Plat4mAPI\Model;

use PDO;

class ProductImage
{
    /**
     * Fetch images list of a product.
     * @param object $ctx Context.
     * @param int $productID Product ID.
     * @return array Images list.
     */
    public function getListByProductID($ctx, $productID)
    {
        $selectSQL = "SELECT `image` FROM `product_images`
            WHERE `product_id` = :productID";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":productID", $productID, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $images = [];

        foreach ($rows as $row) {
            $images[] = URL_HOST . "/" . $row["image"];
        }

        return $images;
    }
}

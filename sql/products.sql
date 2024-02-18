-- Products count.
SELECT COUNT(*) FROM `product_details`
WHERE `storeadmin_id` = :storeAdminID;

-- Products count with filter.
SELECT COUNT(*) FROM `products` `p`
LEFT JOIN `product_details` `pd` ON `pd`.`product_id` = `p`.`product_id`
WHERE `storeadmin_id` = :storeAdminID
AND `product_name` LIKE :productName OR `UPC` LIKE :upc;

-- Products with filter.
SELECT
    p.product_id,
    p.id,
    p.product_name,
    p.UPC,
    pd.quantity,
    pd.Regular_Price,
    pd.sellprice,
    img.image
FROM `products` `p`
LEFT JOIN `product_details` `pd` ON `pd`.`product_id` = `p`.`product_id`
LEFT JOIN `product_images` `img` ON `img`.`product_id` = `p`.`product_id`
WHERE `pd`.`storeadmin_id` = :storeAdminID
AND `product_name` LIKE :productName OR `UPC` LIKE :upc
ORDER BY :columnName :columnSortOrder
LIMIT :offset, :rowCount;

-- Price range.
SELECT MIN(`price`), MAX(`price`)
FROM
    (
        (
            SELECT `sellprice` AS `price`
            FROM `products` `p`
            JOIN `product_details` `pd` ON `pd`.`product_id` = `p`.`product_id`
            WHERE `p`.`UPC` = '811538019569'
        )
        UNION ALL
        (
            SELECT `price`
            FROM `products_temp`
            WHERE `upc` = '811538019569'
        )
        UNION ALL
        (
            SELECT `price`
            FROM `products_temp2`
            WHERE `upc` = '811538019569'
        )
    ) AS `t1`;
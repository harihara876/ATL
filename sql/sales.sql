SELECT
    `product_name`,
    SUM(`quantity`) AS `quantity`,
    FORMAT(SUM(`sellprice`), 2) AS `selling_price`
FROM ordered_product
WHERE `order_id` IN (
    SELECT `id` FROM `users_orders` WHERE `uid` IN (
        SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
        UNION
        SELECT :storeAdminID2
    ) AND `order_status` = :orderStatus
    AND `order_date` BETWEEN :fromDate AND :toDate
)
GROUP BY `product_name`
ORDER BY `quantity` DESC, `product_name` ASC;


-- Example
SELECT
    `product_name`,
    SUM(`quantity`) AS `quantity`,
    FORMAT(SUM(`sellprice`), 2) AS `selling_price`
FROM ordered_product
WHERE `order_id` IN (
    SELECT `id` FROM `users_orders` WHERE `uid` IN (
        SELECT `id` FROM `device_users` WHERE `storeadmin_id` = 26
        UNION
        SELECT 26
    ) AND `order_status` = 'Complete'
    AND `order_date` BETWEEN '2021-06-12 00:00:00' AND '2021-06-12 23:59:59'
)
GROUP BY `product_name`
ORDER BY `quantity` DESC, `product_name` ASC;
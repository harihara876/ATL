ALTER TABLE `products_temp2`
ADD COLUMN `email` VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `checkbit` INT NULL DEFAULT NULL;

ALTER TABLE `products_temp2`
ADD COLUMN `latitude` VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `longitude` VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `localtime` VARCHAR(255) NULL DEFAULT  NULL;

ALTER TABLE `users_orders`
ADD COLUMN `latitude` VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `longitude` VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `localtime` VARCHAR(255) NULL DEFAULT  NULL;

ALTER TABLE `users_orders`
ADD COLUMN `weather` JSON NULL DEFAULT NULL;

ALTER TABLE `products_temp2`
ADD COLUMN `weather` JSON NULL DEFAULT NULL;

ALTER TABLE `admin`
ADD COLUMN `paytm_credentials` TEXT NULL DEFAULT NULL;

-- Remove $ sign for prices.
UPDATE product_details
SET
    price = REPLACE(price, '$', ''),
    sellprice = REPLACE(sellprice, '$', ''),
    Regular_Price = REPLACE(Regular_Price, '$', ''),
    Buying_Price = REPLACE(Buying_Price, '$', '');

UPDATE ordered_product
SET sellprice = REPLACE(sellprice, '$', '');

UPDATE products_temp
SET
    price = REPLACE(price, '$', ''),
    selling_price = REPLACE(selling_price, '$', ''),
    regular_price = REPLACE(regular_price, '$', ''),
    buying_price = REPLACE(buying_price, '$', '');

UPDATE products_temp2
SET price = REPLACE(price, '$', ''),
    selling_price = REPLACE(selling_price, '$', ''),
    regular_price = REPLACE(regular_price, '$', ''),
    buying_price = REPLACE(buying_price, '$', '');


-- Verify if there are any $ symbols.
SELECT * FROM product_details
WHERE price LIKE '%$%'
OR sellprice LIKE '%$%'
OR Regular_Price LIKE '%$%'
OR Buying_Price LIKE '%$%';

SELECT * FROM products_temp
WHERE price LIKE '%$%'
OR selling_price LIKE '%$%'
OR regular_price LIKE '%$%'
OR buying_price LIKE '%$%';

SELECT * FROM products_temp2
WHERE price LIKE '%$%'
OR selling_price LIKE '%$%'
OR regular_price LIKE '%$%'
OR buying_price LIKE '%$%';

SELECT * FROM ordered_product
WHERE sellprice LIKE '%$%';


-- Convert prices from string to decimal.
ALTER TABLE `awg825y6_plat4m`.`product_details`
CHANGE COLUMN `price` `price` DECIMAL(10,2) NOT NULL DEFAULT 0.0 ,
CHANGE COLUMN `sellprice` `sellprice` DECIMAL(10,2) NOT NULL DEFAULT 0.0 ,
CHANGE COLUMN `Regular_Price` `Regular_Price` DECIMAL(10,2) NULL DEFAULT NULL ,
CHANGE COLUMN `Buying_Price` `Buying_Price` DECIMAL(10,2) NULL DEFAULT NULL ;

ALTER TABLE `awg825y6_plat4m`.`products_temp`
CHANGE COLUMN `price` `price` DECIMAL(10,2) NULL DEFAULT NULL ,
CHANGE COLUMN `selling_price` `selling_price` DECIMAL(10,2) NULL DEFAULT NULL ,
CHANGE COLUMN `regular_price` `regular_price` DECIMAL(10,2) NULL DEFAULT NULL ,
CHANGE COLUMN `buying_price` `buying_price` DECIMAL(10,2) NULL DEFAULT NULL ;

ALTER TABLE `awg825y6_plat4m`.`products_temp2`
CHANGE COLUMN `price` `price` DECIMAL(10,2) NULL DEFAULT NULL ,
CHANGE COLUMN `selling_price` `selling_price` DECIMAL(10,2) NULL DEFAULT NULL ,
CHANGE COLUMN `regular_price` `regular_price` DECIMAL(10,2) NULL DEFAULT NULL ,
CHANGE COLUMN `buying_price` `buying_price` DECIMAL(10,2) NULL DEFAULT NULL ;

ALTER TABLE `awg825y6_plat4m`.`ordered_product`
CHANGE COLUMN `sellprice` `sellprice` DECIMAL(10,2) NOT NULL ,
CHANGE COLUMN `tax` `tax` DECIMAL(10,2) NOT NULL DEFAULT '0.00' ;


-- Create index for order_id in users_orders table.
CREATE INDEX `idx_order_serial` ON `users_orders`(`order_id`);

-- Store currency symbol.
ALTER TABLE `admin`
ADD COLUMN `currency_symbol` VARCHAR(10) NOT NULL DEFAULT '$' AFTER `currency`;

ALTER TABLE `awg825y6_plat4m`.`product_details`
ADD COLUMN `multi_item_quantity` INT NULL DEFAULT NULL,
ADD COLUMN `multi_item_price` DECIMAL(10,2) NULL DEFAULT NULL;

ALTER TABLE `awg825y6_plat4m`.`products_temp`
ADD COLUMN `multi_item_quantity` INT NULL DEFAULT NULL,
ADD COLUMN `multi_item_price` DECIMAL(10,2) NULL DEFAULT NULL;

ALTER TABLE `awg825y6_plat4m`.`products_temp2`
ADD COLUMN `multi_item_quantity` INT NULL DEFAULT NULL,
ADD COLUMN `multi_item_price` DECIMAL(10,2) NULL DEFAULT NULL;

ALTER TABLE `awg825y6_plat4m`.`product_details`
ADD COLUMN `discount_percent` INT NOT NULL DEFAULT 0,
ADD COLUMN `discount_pretax` TINYINT NOT NULL DEFAULT 0,
ADD COLUMN `discount_posttax` TINYINT NOT NULL DEFAULT 0;

ALTER TABLE `awg825y6_plat4m`.`products_temp`
ADD COLUMN `discount_percent` INT NOT NULL DEFAULT 0,
ADD COLUMN `discount_pretax` TINYINT NOT NULL DEFAULT 0,
ADD COLUMN `discount_posttax` TINYINT NOT NULL DEFAULT 0;

ALTER TABLE `awg825y6_plat4m`.`products_temp2`
ADD COLUMN `discount_percent` INT NOT NULL DEFAULT 0,
ADD COLUMN `discount_pretax` TINYINT NOT NULL DEFAULT 0,
ADD COLUMN `discount_posttax` TINYINT NOT NULL DEFAULT 0;

-- Column to store password hash.
ALTER TABLE `awg825y6_plat4m`.`admin`
ADD COLUMN `pwhash` VARCHAR(255) NULL DEFAULT NULL
AFTER `password`;
-- Added in all 3 envs.

-- Change charset and collation.
UPDATE product_details SET Date_Created = created_date_on;
UPDATE product_images SET created_time = '2021-05-27 02:52:56';
UPDATE products SET Date_Created = created_time;
ALTER TABLE `awg825y6_plat4m`.`users_profile`
CHARACTER SET = utf8mb4 , COLLATE = utf8mb4_general_ci , ENGINE = InnoDB ;
ALTER DATABASE `awg825y6_plat4m` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
SET foreign_key_checks = 0;
ALTER TABLE `awg825y6_plat4m`.`admin` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `awg825y6_plat4m`.`category` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `awg825y6_plat4m`.`devices` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `awg825y6_plat4m`.`device_users` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `awg825y6_plat4m`.`faq` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `awg825y6_plat4m`.`logs` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `awg825y6_plat4m`.`ordered_product` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `awg825y6_plat4m`.`policy` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `awg825y6_plat4m`.`product_details` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `awg825y6_plat4m`.`product_images` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `awg825y6_plat4m`.`products` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `awg825y6_plat4m`.`products_temp` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `awg825y6_plat4m`.`products_temp2` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `awg825y6_plat4m`.`slider` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `awg825y6_plat4m`.`store_admin_otp` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `awg825y6_plat4m`.`subcategories` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `awg825y6_plat4m`.`subsubcategories` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `awg825y6_plat4m`.`users` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `awg825y6_plat4m`.`users_cart` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `awg825y6_plat4m`.`users_orders` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `awg825y6_plat4m`.`users_profile` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `awg825y6_plat4m`.`users_wishlist` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
SET foreign_key_checks = 1;

ALTER TABLE `awg825y6_plat4m`.`admin`
RENAME COLUMN `compney` TO `store_name`;

ALTER TABLE `awg825y6_plat4m`.`admin`
ADD COLUMN `allowed_cashiers` int NOT NULL DEFAULT 1;

ALTER TABLE `awg825y6_plat4m`.`admin`
ADD COLUMN `allowed_logins` int NOT NULL DEFAULT 1;

ALTER TABLE `awg825y6_plat4m`.`device_users`
ADD COLUMN `allowed_logins` int NOT NULL DEFAULT 1;

ALTER TABLE `awg825y6_plat4m`.`admin`
ADD COLUMN `first_name` varchar(255) NOT NULL DEFAULT "",
ADD COLUMN `last_name` varchar(255) NOT NULL DEFAULT ""
AFTER `name`;

ALTER TABLE `awg825y6_plat4m`.`admin`
MODIFY `first_name` varchar(255) NOT NULL DEFAULT ""
AFTER `name`;

ALTER TABLE `awg825y6_plat4m`.`admin`
ADD COLUMN `registered_app` varchar(255) NOT NULL DEFAULT "com.plat4minc.mystore";

ALTER TABLE `awg825y6_plat4m`.`device_users`
ADD COLUMN `registered_app` varchar(255) NOT NULL DEFAULT "com.plat4minc.mystore";

-- Store admin login attempts.
CREATE TABLE `admin_login`
(
    `id` int NOT NULL AUTO_INCREMENT,
    `admin_id` int NOT NULL,
    `app_name` varchar(255) NOT NULL DEFAULT "",
    `app_instance_id` varchar(255) NOT NULL DEFAULT "",
    `app_device` varchar(255) NOT NULL DEFAULT "",
    `app_version` varchar(255) NOT NULL DEFAULT "",
    `app_platform` varchar(255) NOT NULL DEFAULT "",
    `user_agent` varchar(255) NOT NULL DEFAULT "",
    `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    CONSTRAINT `fk_admin_in_login`
        FOREIGN KEY (`admin_id`)
        REFERENCES `admin` (`admin_id`),
    KEY `idx_admin_app_name` (`app_name`),
    KEY `idx_admin_app_instance_id` (`app_instance_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `cashier_login`
(
    `id` int NOT NULL AUTO_INCREMENT,
    `cashier_id` int UNSIGNED NOT NULL,
    `app_name` varchar(255) NOT NULL DEFAULT "",
    `app_instance_id` varchar(255) NOT NULL DEFAULT "",
    `app_device` varchar(255) NOT NULL DEFAULT "",
    `app_version` varchar(255) NOT NULL DEFAULT "",
    `app_platform` varchar(255) NOT NULL DEFAULT "",
    `user_agent` varchar(255) NOT NULL DEFAULT "",
    `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    CONSTRAINT `fk_cashier_in_login`
        FOREIGN KEY (`cashier_id`)
        REFERENCES `device_users` (`id`),
    KEY `idx_cashier_app_name` (`app_name`),
    KEY `idx_cashier_app_instance_id` (`app_instance_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `awg825y6_plat4m`.`admin`
ADD COLUMN `store_type` varchar(255) NULL DEFAULT NULL,
ADD COLUMN `store_city` varchar(255) NULL DEFAULT NULL,
ADD COLUMN `store_zip` varchar(255) NULL DEFAULT NULL,
ADD COLUMN `store_country` varchar(255) NULL DEFAULT NULL;

update device_users set type_app_admin = "cashier";

ALTER TABLE `awg825y6_plat4m`.`store_admin_otp`
ADD COLUMN `registered_app` varchar(255) NOT NULL DEFAULT "com.plat4minc.mystore";

ALTER TABLE `awg825y6_plat4m`.`store_admin_otp`
ALTER COLUMN `registered_app` DROP DEFAULT;

-- Create index for registered apps.
CREATE INDEX `idx_admin_reg_app` ON `awg825y6_plat4m`.`admin` (`registered_app`);
CREATE INDEX `idx_cashier_reg_app` ON `awg825y6_plat4m`.`device_users` (`registered_app`);
CREATE INDEX `idx_admin_reg_app_otp` ON `awg825y6_plat4m`.`store_admin_otp` (`registered_app`);

ALTER TABLE `awg825y6_plat4m`.`device_users`
ADD COLUMN `mobile_number` varchar(255) NULL DEFAULT NULL;


UPDATE `awg825y6_plat4m`.`products_temp`
SET `date` = `created_on`
WHERE CAST(`date` AS CHAR(20)) = '0000-00-00 00:00:00';

UPDATE `awg825y6_plat4m`.`products_temp`
SET `date_created` = `created_on`
WHERE CAST(`date_created` AS CHAR(20)) = '0000-00-00 00:00:00';

-- Change engine to InnoDB for all tables.
-- List commands.
SELECT CONCAT('ALTER TABLE ',TABLE_NAME,' ENGINE=InnoDB;')
FROM INFORMATION_SCHEMA.TABLES
WHERE table_schema = 'awg825y6_plat4m';

ALTER TABLE admin ENGINE=InnoDB;
ALTER TABLE admin_login ENGINE=InnoDB;
ALTER TABLE cashier_login ENGINE=InnoDB;
ALTER TABLE category ENGINE=InnoDB;
ALTER TABLE device_users ENGINE=InnoDB;
ALTER TABLE devices ENGINE=InnoDB;
ALTER TABLE faq ENGINE=InnoDB;
ALTER TABLE logs ENGINE=InnoDB;
ALTER TABLE ordered_product ENGINE=InnoDB;
ALTER TABLE policy ENGINE=InnoDB;
ALTER TABLE product_details ENGINE=InnoDB;
ALTER TABLE product_images ENGINE=InnoDB;
ALTER TABLE products ENGINE=InnoDB;
ALTER TABLE products_temp ENGINE=InnoDB;
ALTER TABLE products_temp2 ENGINE=InnoDB;
ALTER TABLE slider ENGINE=InnoDB;
ALTER TABLE store_admin_otp ENGINE=InnoDB;
ALTER TABLE subcategories ENGINE=InnoDB;
ALTER TABLE subsubcategories ENGINE=InnoDB;
ALTER TABLE users ENGINE=InnoDB;
ALTER TABLE users_cart ENGINE=InnoDB;
ALTER TABLE users_orders ENGINE=InnoDB;
ALTER TABLE users_profile ENGINE=InnoDB;
ALTER TABLE users_wishlist ENGINE=InnoDB;
ALTER TABLE v2_store ENGINE=InnoDB;
ALTER TABLE v2_user ENGINE=InnoDB;

ALTER TABLE `awg825y6_plat4m`.`product_details`
ADD COLUMN `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN `updated_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP;

UPDATE `awg825y6_plat4m`.`product_details`
SET `created_on` = `created_date_on`,
    `updated_on` = `created_date_on`;

ALTER TABLE `awg825y6_plat4m`.`category`
ADD COLUMN `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN `updated_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE `awg825y6_plat4m`.`subcategories`
ADD COLUMN `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN `updated_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE `awg825y6_plat4m`.`subsubcategories`
ADD COLUMN `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN `updated_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE `awg825y6_plat4m`.`users_orders`
ADD COLUMN `admin_id` int NULL DEFAULT NULL,
ADD COLUMN `cashier_id` int UNSIGNED NULL DEFAULT NULL,
ADD CONSTRAINT `fk_admin_in_users_orders`
        FOREIGN KEY (`admin_id`)
        REFERENCES `admin` (`admin_id`)
        ON UPDATE CASCADE,
ADD CONSTRAINT `fk_cashier_in_users_orders`
        FOREIGN KEY (`cashier_id`)
        REFERENCES `device_users` (`id`)
        ON UPDATE CASCADE;

ALTER TABLE `awg825y6_plat4m`.`users_orders`
ADD COLUMN `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN `updated_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP;

UPDATE `awg825y6_plat4m`.`users_orders`
SET
    `created_on` = `order_date`,
    `updated_on` = `order_date`;

CREATE TABLE `store_cashier_otp` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
    `otp` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
    `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `registered_app` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_cashier_reg_app_otp` (`registered_app`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `store_admin_otp` ADD `event` VARCHAR(250) NOT NULL DEFAULT '' AFTER `email`;
ALTER TABLE `store_cashier_otp` ADD `event` VARCHAR(250) NOT NULL DEFAULT '' AFTER `email`;

UPDATE `store_admin_otp` SET `event` = 'reset-password';
UPDATE `store_cashier_otp` SET `event` = 'reset-password';

ALTER TABLE `products_temp`
CHANGE `upc_status_request` `upc_status_request` ENUM('0','1')
    CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '1';

ALTER TABLE `products_temp`
    CHANGE `multi_item_quantity` `multi_item_qty_one` INT(11) NULL DEFAULT NULL;
ALTER TABLE `products_temp` 
    CHANGE `multi_item_price` `multi_item_price_one` DECIMAL(10,2) NULL DEFAULT NULL;
ALTER TABLE `products_temp` 
    ADD `multi_item_qty_two` INT(11) NULL DEFAULT NULL AFTER `multi_item_price_one`,
    ADD `multi_item_price_two` DECIMAL(10,2) NULL DEFAULT NULL AFTER `multi_item_qty_two`, 
    ADD `multi_item_qty_three` INT(11) NULL DEFAULT NULL AFTER `multi_item_price_two`, 
    ADD `multi_item_price_three` DECIMAL(10,2) NULL DEFAULT NULL AFTER `multi_item_qty_three`;
    
ALTER TABLE `product_details` 
    CHANGE `multi_item_quantity` `multi_item_qty_one` INT(11) NULL DEFAULT NULL;
ALTER TABLE `product_details` 
    CHANGE `multi_item_price` `multi_item_price_one` DECIMAL(10,2) NULL DEFAULT NULL;
ALTER TABLE `product_details` 
    ADD `multi_item_qty_two` INT(11) NULL DEFAULT NULL AFTER `multi_item_price_one`, 
    ADD `multi_item_price_two` DECIMAL(10,2) NULL DEFAULT NULL AFTER `multi_item_qty_two`, 
    ADD `multi_item_qty_three` INT(11) NULL DEFAULT NULL AFTER `multi_item_price_two`, 
    ADD `multi_item_price_three` DECIMAL(10,2) NULL DEFAULT NULL AFTER `multi_item_qty_three`;

ALTER TABLE `product_details` 
    CHANGE `multi_item_qty_one` `multi_item_qty_one` INT(11) NULL DEFAULT '0', 
    CHANGE `multi_item_price_one` `multi_item_price_one` DECIMAL(10,2) NULL DEFAULT '0.00',
    CHANGE `multi_item_qty_two` `multi_item_qty_two` INT(11) NULL DEFAULT '0',
    CHANGE `multi_item_price_two` `multi_item_price_two` DECIMAL(10,2) NULL DEFAULT '0.00', 
    CHANGE `multi_item_qty_three` `multi_item_qty_three` INT(11) NULL DEFAULT '0', 
    CHANGE `multi_item_price_three` `multi_item_price_three` DECIMAL(10,2) NULL DEFAULT '0.00';

ALTER TABLE `products_temp` 
    CHANGE `multi_item_qty_one` `multi_item_qty_one` INT(11) NULL DEFAULT '0', 
    CHANGE `multi_item_price_one` `multi_item_price_one` DECIMAL(10,2) NULL DEFAULT '0.00', 
    CHANGE `multi_item_qty_two` `multi_item_qty_two` INT(11) NULL DEFAULT '0', 
    CHANGE `multi_item_price_two` `multi_item_price_two` DECIMAL(10,2) NULL DEFAULT '0.00', 
    CHANGE `multi_item_qty_three` `multi_item_qty_three` INT(11) NULL DEFAULT '0', 
    CHANGE `multi_item_price_three` `multi_item_price_three` DECIMAL(10,2) NULL DEFAULT '0.00';

UPDATE `products_temp` 
    SET `multi_item_qty_one`= IFNULL(multi_item_qty_one,0),
        `multi_item_price_one`= IFNULL(multi_item_price_one,0.00),
        `multi_item_qty_two`= IFNULL(multi_item_qty_two,0),
        `multi_item_price_two`= IFNULL(multi_item_price_two,0.00),
        `multi_item_qty_three`= IFNULL(multi_item_qty_three,0),
        `multi_item_price_three`= IFNULL(multi_item_price_three,0.00);

UPDATE `product_details` 
    SET `multi_item_qty_one`= IFNULL(multi_item_qty_one,0),
        `multi_item_price_one`= IFNULL(multi_item_price_one,0.00),
        `multi_item_qty_two`= IFNULL(multi_item_qty_two,0),
        `multi_item_price_two`= IFNULL(multi_item_price_two,0.00),
        `multi_item_qty_three`= IFNULL(multi_item_qty_three,0),
        `multi_item_price_three`= IFNULL(multi_item_price_three,0.00);

ALTER TABLE `awg825y6_plat4m`.`product_details` ADD COLUMN `product_name` VARCHAR(255) NOT NULL AFTER `description`;
ALTER TABLE `awg825y6_plat4m`.`admin` ADD COLUMN `store_state` VARCHAR(255) NOT NULL AFTER `store_type`;

CREATE TABLE `awg825y6_plat4m`.`verify_email` 
( `id` INT NOT NULL AUTO_INCREMENT , 
    `email` VARCHAR(255) NOT NULL , 
    `event` VARCHAR(250) NOT NULL , 
    `otp` VARCHAR(250) NOT NULL , 
    `created_on` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

ALTER TABLE `awg825y6_plat4m`.`users_orders` 
    ADD COLUMN `total_tax` DECIMAL(10,5) NOT NULL DEFAULT '0.00000'  AFTER `total`, 
    ADD COLUMN `total_special_fee` DECIMAL(10,2) NOT NULL  AFTER `total_tax`;
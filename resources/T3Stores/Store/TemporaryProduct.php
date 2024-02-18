<?php

namespace T3Stores\Store;

use \PDO;
use \PDOException;
use Exception;

class TemporaryProduct
{
    // DB connection object.
    private $db;

    // Product info.
    private $productInfo = [
        "product_name"          => NULL,
        "product_id"            => NULL,
        "cat_id"                => NULL,
        "description"           => NULL,
        "price"                 => NULL,
        "selling_price"         => NULL,
        "color"                 => NULL,
        "size"                  => NULL,
        "product_status"        => NULL,
        "quantity"              => NULL,
        "date"                  => NULL,
        "p_limit"               => NULL,
        "upc"                   => NULL,
        "regular_price"         => NULL,
        "buying_price"          => NULL,
        "tax_status"            => NULL,
        "tax_value"             => NULL,
        "special_value"         => NULL,
        "category_id"           => NULL,
        "category_type"         => NULL,
        "date_created"          => NULL,
        "sku"                   => NULL,
        "image"                 => NULL,
        "stock_quantity"        => NULL,
        "manufacturer"          => NULL,
        "brand"                 => NULL,
        "vendor"                => NULL,
        "product_mode"          => NULL,
        "age_restriction"       => NULL,
        "sale_type"             => NULL,
        "upc_status_request"    => NULL,
        "storeadmin_id"         => NULL,
    ];



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



    /**
     * Sets product info.
     * @param array $info Product info.
     * @return object Current object.
     */
    public function setInfo($info)
    {
        // TODO
        // Validate

        foreach ($this->productInfo as $key => $value) {
            $this->productInfo[$key] = !empty($info[$key]) ? $info[$key] : NULL;
        }

        return $this;
    }



    /**
     * Creates product.
     * @return int Last insert ID.
     */
    public function create()
    {

        //$selectSQL = "SELECT max(product_id) as product_id FROM `products`";
        $selectSQL = "SELECT IFNULL(MAX(product_id), 0) product_id FROM(SELECT product_id FROM products UNION ALL SELECT product_id FROM products_temp) a";
        $product_id = $this->db->query($selectSQL)->fetch(PDO::FETCH_OBJ);
        $product_id = ($product_id->product_id) + 1;


        $info = $this->productInfo;
        $insertSQL = "INSERT INTO `products_temp` (
            `product_name`,
            `product_id`,
            `cat_id`,
            `description`,
            `price`,
            `selling_price`,
            `color`,
            `size`,
            `product_status`,
            `quantity`,
            `date`,
            `p_limit`,
            `upc`,
            `regular_price`,
            `buying_price`,
            `tax_status`,
            `tax_value`,
            `special_value`,
            `category_id`,
            `category_type`,
            `date_created`,
            `sku`,
            `image`,
            `stock_quantity`,
            `manufacturer`,
            `brand`,
            `vendor`,
            `product_mode`,
            `age_restriction`,
            `sale_type`,
			`upc_status_request`,
			`storeadmin_id`
        ) VALUES (
            :product_name,
            :product_id,
            :cat_id,
            :description,
            :price,
            :selling_price,
            :color,
            :size,
            :product_status,
            :quantity,
            :date,
            :p_limit,
            :upc,
            :regular_price,
            :buying_price,
            :tax_status,
            :tax_value,
            :special_value,
            :category_id,
            :category_type,
            :date_created,
            :sku,
            :image,
            :stock_quantity,
            :manufacturer,
            :brand,
            :vendor,
            :product_mode,
            :age_restriction,
            :sale_type,
			:upc_status_request,
			:storeadmin_id
        )";

        try {
            $stmt = $this->db->prepare($insertSQL);
            $info["upc_status_request"] = 1;
            $stmt->bindParam(":product_name", $info["product_name"]);
            $stmt->bindParam(":product_id", $product_id);
            $stmt->bindParam(":cat_id", $info["cat_id"]);
            $stmt->bindParam(":description", $info["description"]);
            $stmt->bindParam(":price", $info["price"]);
            $stmt->bindParam(":selling_price", $info["selling_price"]);
            $stmt->bindParam(":color", $info["color"]);
            $stmt->bindParam(":size", $info["size"]);
            $stmt->bindParam(":product_status", $info["product_status"]);
            $stmt->bindParam(":quantity", $info["quantity"]);
            $stmt->bindParam(":date", $info["date"]);
            $stmt->bindParam(":p_limit", $info["p_limit"]);
            $stmt->bindParam(":upc", $info["upc"]);
            $stmt->bindParam(":regular_price", $info["regular_price"]);
            $stmt->bindParam(":buying_price", $info["buying_price"]);
            $stmt->bindParam(":tax_status", $info["tax_status"]);
            $stmt->bindParam(":tax_value", $info["tax_value"]);
            $stmt->bindParam(":special_value", $info["special_value"]);
            $stmt->bindParam(":category_id", $info["category_id"]);
            $stmt->bindParam(":category_type", $info["category_type"]);
            $stmt->bindParam(":date_created", $info["date_created"]);
            $stmt->bindParam(":sku", $info["sku"]);
            $stmt->bindParam(":image", $info["image"]);
            $stmt->bindParam(":stock_quantity", $info["stock_quantity"]);
            $stmt->bindParam(":manufacturer", $info["manufacturer"]);
            $stmt->bindParam(":brand", $info["brand"]);
            $stmt->bindParam(":vendor", $info["vendor"]);
            $stmt->bindParam(":product_mode", $info["product_mode"]);
            $stmt->bindParam(":age_restriction", $info["age_restriction"]);
            $stmt->bindParam(":sale_type", $info["sale_type"]);
            $stmt->bindParam(":upc_status_request", $info["upc_status_request"]);
            $stmt->bindParam(":storeadmin_id", $info["storeadmin_id"]);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }



    /**
     * Returns all temporary products list.
     * @return array Products.
     */
    public function getAll($storeid)
    {
        $sql = '';
        if ($storeid) {
            $sql = ' where storeadmin_id = ' . $storeid;
        }
        $selectSQL = "SELECT *
            FROM `products_temp` " . $sql . "
            ORDER BY `id` ASC";
        return $this->db->query($selectSQL)->fetchAll(PDO::FETCH_ASSOC);
    }


    public function checkTempProduct($upc, $storeid)
    {
        $selectSQL = "SELECT upc FROM `products_temp`  where `upc`={$upc} AND storeadmin_id=" . $storeid;
        return $this->db->query($selectSQL)->fetch(PDO::FETCH_OBJ);
    }

    public function checkStoreProduct($upc, $storeid)
    {
        $selectSQL = "SELECT UPC FROM `products` p LEFT JOIN product_details pd ON pd.product_id=p.product_id where `UPC`={$upc} AND storeadmin_id=" . $storeid;
        return $this->db->query($selectSQL)->fetch(PDO::FETCH_OBJ);
    }


    public function getStoreProduct($upc, $storeid)
    {
        $selectSQL = "SELECT *,(select CONCAT('https://mystore.plat4minc.com/', image) from  product_images pi where pi.product_id=p.product_id) AS Image FROM `products` p LEFT JOIN product_details pd ON pd.product_id=p.product_id where `UPC`={$upc} AND pd.storeadmin_id=" . $storeid;
        return $this->db->query($selectSQL)->fetch(PDO::FETCH_OBJ);
    }

    public function getCategory($cid)
    {
        $selectSQL = "SELECT * FROM `category` WHERE `cat_id`=" . $cid;
        return $this->db->query($selectSQL)->fetch(PDO::FETCH_OBJ);
    }

    public function getSubCategory($subcid)
    {
        $selectSQL = "SELECT * FROM `subcategories` WHERE `Sub_Category_Id`=" . $subcid;
        return $this->db->query($selectSQL)->fetch(PDO::FETCH_OBJ);
    }

    public function checkProduct($upc)
    {
        $selectSQL = "SELECT UPC FROM `products`  where `UPC`={$upc}";
        return $this->db->query($selectSQL)->fetch(PDO::FETCH_OBJ);
    }

    public function copyProductToStore($upc, $price, $selling_price, $qty, $storeid)
    {

        $selectSQL = "SELECT * FROM `products` where `UPC`={$upc} ";
        $resultcheck = $this->db->query($selectSQL)->fetch(PDO::FETCH_ASSOC);

        $cat_id = $resultcheck['Category_Id'];
        $product_name = $resultcheck['product_name'];
        $product_id = $resultcheck['product_id'];
        $description = $resultcheck['description'];
        $price = $resultcheck['price'];
        $sellprice = $resultcheck['sellprice'];
        $color = $resultcheck['color'];
        $size = $resultcheck['size'];
        $product_status = $resultcheck['product_status'];
        $quantity = $resultcheck['quantity'];
        $date = $resultcheck['date'];
        $plimit = $resultcheck['plimit'];
        $UPC = $resultcheck['UPC'];
        $Regular_Price = $resultcheck['Regular_Price'];
        $Buying_Price = $resultcheck['Buying_Price'];
        $Tax_Status = $resultcheck['Tax_Status'];
        $Tax_Value = $resultcheck['Tax_Value'];
        $Special_Value = $resultcheck['Special_Value'];
        $Category_Id = $resultcheck['Category_Id'];
        $Category_Type = $resultcheck['Category_Type'];
        $Date_Created = $resultcheck['Date_Created'];
        $SKU = $resultcheck['SKU'];
        $Image = $resultcheck['Image'];
        $Stock_Quantity = $resultcheck['Stock_Quantity'];
        $Manufacturer = $resultcheck['Manufacturer'];
        $Brand = $resultcheck['Brand'];
        $Vendor = $resultcheck['Vendor'];
        $ProductMode = $resultcheck['ProductMode'];
        $Age_Restriction = $resultcheck['Age_Restriction'];
        $sale_type = $resultcheck['sale_type'];
        $status = $resultcheck['status'];
        $cat_id = $resultcheck['Category_Id'];

        if ($price) {
            $price = $price;
            $Buying_Price = $price;
        }

        if ($selling_price) {
            $sellprice = $selling_price;
            $Regular_Price = $selling_price;
        }

        if ($qty) {
            $quantity = $qty;
        }


        //$product_insert = "INSERT INTO products ( `product_name`, `product_id`, `cat_id`, `UPC`, `Category_Id`, `Category_Type`, `Date_Created`, `Image`, `Manufacturer`, `Brand`, `Vendor`, `status`) VALUES('{$product_name}', '{$product_id}', '{$cat_id}', '{$UPC}', '{$cat_id}', '{$subcat_id}', '{$Date_Created}', '{$Image}', '{$Manufacturer}', '{$Brand}', '{$Vendor}', '{$status}')";
        //$stmt = $this->db->prepare($product_insert);
        //$stmt->execute();
        //$last_id = $this->db->lastInsertId();

        $product_details_insert = "INSERT INTO product_details (`product_id`, `description`, `price`, `sellprice`, `color`, `size`, `product_status`, `quantity`, `plimit`, `Regular_Price`, `Buying_Price`, `Tax_Status`, `Tax_Value`, `Special_Value`, `Date_Created`, `SKU`, `Stock_Quantity`, `ProductMode`, `Age_Restriction`, `sale_type`, `status`, `storeadmin_id`) VALUES('{$product_id}','{$description}', '{$price}', '{$sellprice}', '{$color}', '{$size}', '{$product_status}', '{$quantity}', '{$plimit}', '{$Regular_Price}', '{$Buying_Price}', '{$Tax_Status}', '{$Tax_Value}', '{$Special_Value}', '{$Date_Created}', '{$SKU}', '{$Stock_Quantity}', '{$ProductMode}', '{$Age_Restriction}', '{$sale_type}', '{$status}','{$storeid}')";
        $stmt2 = $this->db->prepare($product_details_insert);
        $stmt2->execute();
        $last_id = $this->db->lastInsertId();

        return $last_id;
    }
}

<?php

use Plat4mAPI\Model\Category;
use Plat4mAPI\Model\ProductCatalogue;
use Plat4mAPI\Model\ProductImage;
use Plat4mAPI\Model\StoreProduct;
use Plat4mAPI\Model\StoreTempProduct;
use Plat4mAPI\Model\StoreTempProduct2;
use Plat4mAPI\Model\Subcategory;
use Plat4mAPI\Model\TempProduct2;
use Plat4mAPI\Util\Logger;
use Plat4mAPI\Util\Uploader;
use Plat4mAPI\Util\Validator;
use Plat4mAPI\Util\Weather;

use Plat4mAPI\Util\Mailer;

/**
 * Get all store products handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function getStoreProductsV1($ctx, $args)
{
    
    $storeProductModel = new StoreProduct($ctx->tokenData->store_admin_id);
    $products = $storeProductModel->getAll($ctx);

    foreach ($products as &$product) {
        $product["multi_item_qty_one"] = ($product["multi_item_qty_one"] === NULL)
            ? (int) 0
            : (int) ($product["multi_item_qty_one"]);
        $product["multi_item_price_one"] = $product["multi_item_price_one"] ?? $product["regular_price"];
        $product["multi_item_qty_two"] = ($product["multi_item_qty_two"] === NULL) 
            ? (int) 0 
            : (int) ($product["multi_item_qty_two"]);
        $product["multi_item_price_two"] = $product["multi_item_price_two"] ?? $product["regular_price"];
        $product["multi_item_qty_three"] = ($product["multi_item_qty_three"] === NULL)
            ? (int) 0
            : (int) ($product["multi_item_qty_three"]);
        $product["multi_item_price_three"] = $product["multi_item_price_three"] ?? $product["regular_price"];
        $product["store_product_name"] = ($product["store_product_name"] === "")
            ? $product["global_product_name"]
            : ($product["store_product_name"]);
        $product["product_type"] = "STORE-PRODUCT";
        // $product["tax_value"] = ($product["tax_value"] === 0 || $product["tax_value"] === 0.00 || $product["tax_value"] ===     0.0 || $product["tax_value"] === "" && $product["tax_status"] === "CA Tax") 
        //     ? $product["admin_tax_value"]
        //     : $product["tax_value"];

        $product["images"] = (new ProductImage)->getListByProductID($ctx, $product["product_id"]);
    }

    sendJSON(200, [
        "store_products" => $products
    ]);
}

/**
 * Get all store temp products handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function getStoreTempProductsV1($ctx, $args)
{
    $tempProductModel = new StoreTempProduct($ctx->tokenData->store_admin_id);
    $products = $tempProductModel->getAll($ctx);

    foreach ($products as &$product) {
        $product["multi_item_qty_one"] = ($product["multi_item_qty_one"] === NULL)
            ? (int) 0
            : (int) ($product["multi_item_qty_one"]);
        $product["multi_item_price_one"] = $product["multi_item_price_one"] ?? $product["regular_price"];
        $product["multi_item_qty_two"] = ($product["multi_item_qty_two"] === NULL)
            ? (int) 0 
            : (int) ($product["multi_item_qty_two"]);
        $product["multi_item_price_two"] = $product["multi_item_price_two"] ?? $product["regular_price"];
        $product["multi_item_qty_three"] = ($product["multi_item_qty_three"] === NULL)
            ? (int) 0
            : (int) ($product["multi_item_qty_three"]);
        $product["multi_item_price_three"] = $product["multi_item_price_three"] ?? $product["regular_price"];
        $product["product_type"] = "STORE-TEMP-PRODUCT";
    }

    sendJSON(200, ["store_temp_products" => $products]);
}

/**
 * Get all store temp2 products handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function getStoreTemp2ProductsV1($ctx, $args)
{
    $tempProductModel = new StoreTempProduct2($ctx->tokenData->store_admin_id);
    $products = $tempProductModel->getAll($ctx);

    foreach ($products as &$product) {
        $product["multi_item_quantity"] = ($product["multi_item_quantity"] === NULL)
            ? (int) 0
            : (int) ($product["multi_item_quantity"]);
        $product["multi_item_price"] = $product["multi_item_price"] ?? $product["regular_price"];
    }

    sendJSON(200, ["store_temp2_products" => $products]);
}

/**
 * Get store product handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function getStoreProductV1($ctx, $args)
{
    // Fetch product details from catalogue instead of store.
    $productCatalogueModel = new ProductCatalogue;
    $product = $productCatalogueModel->getInfoByUPC($ctx, $args["upc"]);
    // print_r($product);exit();

    if (!$product) {
        sendErrJSON(404, ERR_PRODUCT_NOT_FOUND);
    }

    $product = $productCatalogueModel->format($product);

    $product["store_product_name"] = ($product["store_product_name"] === "")
        ? $product["global_product_name"]
        : ($product["store_product_name"]);
    $category = (new Category)->getInfoByID($ctx, $product["category_id"]);
    $subcategory = (new Subcategory)->getInfoByID($ctx, $product["subcategory_id"]);
    $images = (new ProductImage)->getListByProductID($ctx, $product["product_id"]);

    sendJSON(200, [
        "product"       => $product,
        "category"      => $category ? $category : NULL,
        "subcategory"   => $subcategory ? $subcategory : NULL,
        "images"        => $images,
    ]);
}

/**
 * Product price range handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function getProductPriceRangeV1($ctx, $args)
{
    // Validate input.
    $v = new Validator;
    $v->name("UPC")->str($args["upc"])->reqStr();
    $v->name("Product ID")->str($args["product_id"])->reqStr();

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $productExists = (new productCatalogue)->productExists($ctx,$args["product_id"]);
    $productExists = empty($productExists) ? 
                     (new productCatalogue)->storeProductExists($ctx,$args["product_id"]) : $productExists;

    $upcExists = (new productCatalogue)->upcExistsInProducts($ctx, $args["upc"]);
    $upcExists = empty($upcExists) ? 
                (new StoreTempProduct($ctx->tokenData->store_admin_id))->upcExists($ctx, $args["upc"]) : $upcExists;

    $prices = (new ProductCatalogue)->getSellingPriceRangeAcrossAll($ctx, $args["upc"]);
    $images = (new ProductImage)->getListByProductID($ctx, $args["product_id"]);
    $tempImages = (new StoreTempProduct($ctx->tokenData->store_admin_id))->getImagesByUPC($ctx, $args["upc"]);
    $temp2Images = (new StoreTempProduct2($ctx->tokenData->store_admin_id))->getImagesByUPC($ctx, $args["upc"]);

    $images = !empty($productExists) ? array_merge($images, $tempImages, $temp2Images) : 
                array("Please enter valid product id");

    if (empty($productExists) && empty($upcExists)) {
         sendErrJSON(404, ERR_PRODUCT_AND_UPC_NOT_FOUND);
    }

    if (empty($upcExists)) {
       sendErrJSON(404, ERR_UPC_NOT_FOUND);
    }

    sendJSON(200, [
        "min_selling_price" => $prices["min_price"],
        "max_selling_price" => $prices["max_price"],
        "product_images"    => $images,
    ]);
}

/**
 * Check UPC handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function checkUPCV1($ctx, $args)
{
    // Fetch from products table.
    $productModel = new ProductCatalogue();
    $product = $productModel->getOBCRInfoByUPC($ctx, $args["upc"]);

    if ($product) {
        Logger::infoMsg(sprintf("UPC: %s found in products.", $args["upc"]));
        
        $category = ($product["category_id"])
            ? (new Category)->getInfoByID($ctx, $product["category_id"])
            : NULL;
        $subcategory = ($product["subcategory_id"])
            ? (new Subcategory)->getInfoByID($ctx, $product["subcategory_id"])
            : NULL;

        sendJSON(200, [
            "product"       => $product,
            "category"      => $category,
            "subcategory"   => $subcategory,
        ]);
    }

    // Fetch from products_temp2 table.
    $temp2ProductModel = new TempProduct2();
    $product = $temp2ProductModel->getInfoByUPC($ctx, $args["upc"]);

    if ($product) {
        Logger::infoMsg(sprintf("UPC: %s found in temp2 products.", $args["upc"]));

        $category = ($product["category_id"])
            ? (new Category)->getInfoByID($ctx, $product["category_id"])
            : NULL;
        $subcategory = ($product["subcategory_id"])
            ? (new Subcategory)->getInfoByID($ctx, $product["subcategory_id"])
            : NULL;

        sendJSON(200, [
            "product"       => $product,
            "category"      => $category,
            "subcategory"   => $subcategory,
        ]);
    }

    sendErrJSON(404, ERR_UPC_NOT_FOUND);
}

/**
 * Copy catalogue product to store handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function copyCatalogueProductToStoreV1($ctx, $args)
{
    $payload = payload();
    $input = [
        "regular_price"             => arrVal($payload, "regular_price"),
        "multi_item_qty_one"        => arrVal($payload, "multi_item_qty_one"),
        "multi_item_price_one"      => arrVal($payload, "multi_item_price_one"),
        "multi_item_qty_two"        => arrVal($payload, "multi_item_qty_two"),
        "multi_item_price_two"      => arrVal($payload, "multi_item_price_two"),
        "multi_item_qty_three"      => arrVal($payload, "multi_item_qty_three"),
        "multi_item_price_three"    => arrVal($payload, "multi_item_price_three"),
        "discount_percent"          => arrVal($payload, "discount_percent"),
        "discount_pretax"           => arrVal($payload, "discount_pretax"),
        "discount_posttax"          => arrVal($payload, "discount_posttax"),
        "stock_quantity"            => arrVal($payload, "stock_quantity"),
        "buying_price"              => arrVal($payload, "buying_price"),
        "selling_price"             => arrVal($payload, "selling_price"),
        "tax_value"                 => arrVal($payload, "tax_value"),
        "tax_status"                => arrVal($payload, "tax_status"),
    ];
    // Validate input.
    $v = new Validator;
    $v->name("UPC")->str($args["upc"])->reqStr();

    if (!empty($input["tax_status"])) {
        $v->name("Tax Status")->str($input["tax_status"])->reqStr();
    }

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    // Fetch product from store.
    $storeProductModel = new StoreProduct($ctx->tokenData->store_admin_id);
    $storeProduct = $storeProductModel->getInfoByUPC($ctx, $args["upc"]);
    if ($storeProduct) {
        sendJSON(201, $storeProduct);
    }

    // Fetch product from catalogue.
    $productModel = new ProductCatalogue;
    $product = $productModel->getInfoByUPC($ctx, $args["upc"]);

    if (!$product['id']) {
        sendErrJSON(404, ERR_UPC_NOT_FOUND, "UPC not found in catalogue");
    }
    
    // Update dynamic fields and copy product details from catalogue to store.
    $product["admin_id"]               = $ctx->tokenData->store_admin_id;
    $product["Date_Created"]           = $ctx->now;
    $product["created_date_on"]        = $ctx->now;
    $product["created_on"]             = $ctx->now;
    $product["updated_on"]             = $ctx->now;

    $product["tax_value"]              = $input["tax_value"];
    $product["tax_status"]             = $input["tax_status"];

    $product["regular_price"]          = empty($input['regular_price']) 
                                        ? $product["regular_price"] : $input['regular_price'];
    $product["multi_item_qty_one"]     = empty($input['multi_item_qty_one']) 
                                        ? $product["multi_item_qty_one"] : $input['multi_item_qty_one'];
    $product["multi_item_price_one"]   = empty($input['multi_item_price_one']) 
                                        ? $product["multi_item_price_one"] : $input['multi_item_price_one'];
    $product["multi_item_qty_two"]     = empty($input['multi_item_qty_two']) 
                                        ? $product["multi_item_price_one"] : $input['multi_item_qty_two'];
    $product["multi_item_price_two"]   = empty($input['multi_item_price_two']) 
                                        ? $product["multi_item_price_two"] : $input['multi_item_price_two'];
    $product["multi_item_qty_three"]   = empty($input['multi_item_qty_three']) 
                                        ? $product["multi_item_qty_three"] : $input['multi_item_qty_three'];
    $product["multi_item_price_three"] = empty($input['multi_item_price_three']) 
                                        ? $product["multi_item_price_three"] : $input['multi_item_price_three'];
    $product["discount_percent"]       = empty($input['discount_percent']) 
                                        ? $product["discount_percent"] : $input['discount_percent'];
    $product["discount_pretax"]        = empty($input['discount_pretax']) 
                                        ? $product["discount_pretax"] : $input['discount_pretax'];
    $product["discount_posttax"]       = empty($input['discount_posttax']) 
                                        ? $product["discount_posttax"] : $input['discount_posttax'];
    $product["stock_quantity"]         = empty($input['stock_quantity']) 
                                        ? 0 : $input['stock_quantity'];
    $product["buying_price"]           = empty($input['buying_price']) 
                                        ? $product["buying_price"] : $input['buying_price'];
    $product["selling_price"]          = empty($input['selling_price']) 
                                        ? $product["selling_price"] : $input['selling_price'];

    $insertID = $productModel->createDetails($ctx, $product);
    if (!$insertID) {
        sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR, "Failed to move product from catalogue to store");
    }

    $storeProduct = $storeProductModel->getInfoByUPC($ctx, $args["upc"]);
    $category = (new Category)->getInfoByID($ctx, $storeProduct["category_id"]);
    $subcategory = (new Subcategory)->getInfoByID($ctx, $storeProduct["subcategory_id"]);
    sendJSON(201, [
        "product"       => $storeProduct,
        "category"      => $category ? $category : NULL,
        "subcategory"   => $subcategory ? $subcategory : NULL,
    ]);

    // sendJSON(201, $storeProduct);
}

/**
 * Update store product handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function updateStoreProductV1($ctx, $args)
{
    $payload = payload();
    $input = [
        "name" => arrVal($payload, "name"),
        "stock_quantity" => arrVal($payload, "stock_update"),
        "regular_price" => arrVal($payload, "regular_price"),
    ];

    // Validate input.
    $v = new Validator;
    $v->name("Product ID")->str($args["product_id"])->reqStr();

    if (isset($payload["stock_quantity"])) {
        $v->name("Stock quantity")->nInt($payload["stock_quantity"]);
    }

    if (isset($payload["name"])) {
        $v->name("name")->str($payload["name"])->reqStr();
    }

    if (isset($payload["regular_price"])) {
        $v->name("Regular Price")->nFloat($payload["regular_price"]);
    }

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    // Fetch product info.
    $storeProductModel = new StoreProduct($ctx->tokenData->store_admin_id);
    $product = $storeProductModel->getInfoByProductID($ctx, $args["product_id"]);

    if (!$product['id']) {
        sendErrJSON(404, ERR_PRODUCT_NOT_FOUND);
    }

    // Update info.
    $data["product_id"] = $args["product_id"];
    $data['name'] = empty($input["name"]) ? $product["store_product_name"] : $input["name"];
    $data['stockQty'] = empty($input["stock_quantity"]) ? 0 + $product["stock_quantity"] : $input["stock_quantity"] + $product["stock_quantity"];
    $data['regularPrice'] = empty($input["regular_price"]) ? $product["regular_price"] : $input["regular_price"];
    
    $affectedRows = $storeProductModel->updateDescription($ctx, $data);

    if ($affectedRows < 1) {
        sendErrJSON(500, ERR_UPDATE_PRODUCT_FAILED);
    }

    sendJSON(200, $storeProductModel->getInfoByProductID($ctx, $args["product_id"]));
}

/**
 * Update store product price handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function updateStoreProductPriceV1($ctx, $args)
{
    $payload = payload();
    $input = [
        "regular_price"         => arrVal($payload, "regular_price"),
        "multi_item_qty_one"    => arrVal($payload, "multi_item_qty_one"),
        "multi_item_price_one"  => arrVal($payload, "multi_item_price_one"),
        "multi_item_qty_two"    => arrVal($payload, "multi_item_qty_two"),
        "multi_item_price_two"  => arrVal($payload, "multi_item_price_two"),
        "multi_item_qty_three"  => arrVal($payload, "multi_item_qty_three"),
        "multi_item_price_three"=> arrVal($payload, "multi_item_price_three"),
        "discount_percent"      => arrVal($payload, "discount_percent", 0),
        "discount_pretax"       => arrVal($payload, "discount_pretax", 0),
        "discount_posttax"      => arrVal($payload, "discount_posttax", 0),
        "stock_quantity"        => arrVal($payload, "stock_update"),
        "buying_price"          => arrVal($payload, "buying_price"),
        "tax_status"            => arrVal($payload, "tax_status"),
        "tax_value"             => arrVal($payload, "tax_value"),
    ];

    // Validate input.
    $v = new Validator;
    $v->name("Product ID")->str($args["product_id"])->reqStr();
    $v->name("Regular Price")->nFloat($input["regular_price"]);
    $v->name("Multi Item Quantity One")->nInt($input["multi_item_qty_one"]);
    $v->name("Multi Item Price One")->nFloat($input["multi_item_price_one"]);
    $v->name("Multi Item Quantity Two")->nInt($input["multi_item_qty_two"]);
    $v->name("Multi Item Price Two")->nFloat($input["multi_item_price_two"]);
    $v->name("Multi Item Quantity Three")->nInt($input["multi_item_qty_three"]);
    $v->name("Multi Item Price Three")->nFloat($input["multi_item_price_three"]);
    $v->name("Discount Percent")->nInt($input["discount_percent"]);
    $v->name("Discount Pretax")->nInt($input["discount_pretax"]);
    $v->name("Discount Posttax")->nInt($input["discount_posttax"]);

    if (isset($payload["stock_quantity"])) {
        $v->name("Stock Quantity")->nInt($payload["stock_quantity"]);
    }

    $v->name("Buying Price")->nFloat($input["buying_price"]);

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    if (!empty($input["tax_status"])) {
        $v->name("Tax Status")->str($input["tax_status"])->reqStr();
    }

    // Fetch product info.
    $storeProductModel = new StoreProduct($ctx->tokenData->store_admin_id);
    $product = $storeProductModel->getInfoByProductID($ctx, $args["product_id"]);

    if (!$product['id']) {
        sendErrJSON(404, ERR_PRODUCT_NOT_FOUND);
    }

    $changed = FALSE;
    $changed = ((float) $product["regular_price"] !== (float) $input["regular_price"]) || $changed;
    $changed = ((int) $product["multi_item_qty_one"] !== (int) $input["multi_item_qty_one"]) || $changed;
    $changed = ((float) $product["multi_item_price_one"] !== (float) $input["multi_item_price_one"]) || $changed;
    $changed = ((int) $product["multi_item_qty_two"] !== (int) $input["multi_item_qty_two"]) || $changed;
    $changed = ((float) $product["multi_item_price_two"] !== (float) $input["multi_item_price_two"]) || $changed;
    $changed = ((int) $product["multi_item_qty_three"] !== (int) $input["multi_item_qty_three"]) || $changed;
    $changed = ((float) $product["multi_item_price_three"] !== (float) $input["multi_item_price_three"]) || $changed;
    $changed = ((int) $product["discount_percent"] !== (int) $input["discount_percent"]) || $changed;
    $changed = ((int) $product["discount_pretax"] !== (int) $input["discount_pretax"]) || $changed;
    $changed = ((int) $product["discount_posttax"] !== (int) $input["discount_posttax"]) || $changed;
    $changed = ((int) $product["stock_quantity"] !== (int) $input["stock_quantity"]) || $changed;
    $changed = ((float) $product["buying_price"] !== (float) $input["buying_price"]) || $changed;

    $priceInfo = new stdClass;
    $priceInfo->regular_price = (float) $input["regular_price"];
    $priceInfo->multi_item_qty_one = (int) $input["multi_item_qty_one"];
    $priceInfo->multi_item_price_one = (float) $input["multi_item_price_one"];
    $priceInfo->multi_item_qty_two = (int) $input["multi_item_qty_two"];
    $priceInfo->multi_item_price_two = (float) $input["multi_item_price_two"];
    $priceInfo->multi_item_qty_three = (int) $input["multi_item_qty_three"];
    $priceInfo->multi_item_price_three = (float) $input["multi_item_price_three"];
    $priceInfo->discount_percent = (int) $input["discount_percent"];
    $priceInfo->discount_pretax = (int) $input["discount_pretax"];
    $priceInfo->discount_posttax = (int) $input["discount_posttax"];
    $priceInfo->stock_quantity = (int) empty($input["stock_quantity"]) ? 0 + $product["stock_quantity"] : $input["stock_quantity"] + $product["stock_quantity"];
    $priceInfo->buying_price = (float) $input["buying_price"];
    $priceInfo->tax_value = (string) $input["tax_value"];
    $priceInfo->tax_status = (string) $input["tax_status"];

    $affectedRows = $storeProductModel->updatePrice($ctx, $args["product_id"], $priceInfo);

    if ($affectedRows < 1) {
        sendErrJSON(500, ERR_UPDATE_PRODUCT_FAILED);
    }

    sendJSON(200, $storeProductModel->getInfoByProductID($ctx, $args["product_id"]));
}

/**
 * Validate temp product.
 * @param array $product Product info.
 */
function validateTempProduct(&$product)
{
    $v = new Validator;
    $v->name("UPC")->str($product["upc"])->reqStr();
    $v->name("Price")->nFloat($product["price"]);
    // $v->name("UPC Status Request")->nInt($product["upc_status_request"]);

    if (isset($product["age_restriction"])) {
        $v->name("Age restriction")->nInt($product["age_restriction"]);
    }

    if (isset($product["brand"])) {
        $v->name("Brand")->str($product["brand"]);
    }

    if (isset($product["buying_price"])) {
        $v->name("Buying Price")->nFloat($product["buying_price"]);
    }

    if (isset($product["category_id"])) {
        $v->name("Category ID")->nInt($product["category_id"]);
    }

    if (isset($product["subcategory_id"])) {
        $v->name("Subcategory ID")->nInt($product["subcategory_id"]);
    }

    if (isset($product["color"])) {
        $v->name("Color")->str($product["color"]);
    }

    if (isset($product["description"])) {
        $v->name("Description")->str($product["description"]);
    }

    if (isset($product["discount_percent"])) {
        $v->name("Discount percent")->nInt($product["discount_percent"]);
    }

    if (isset($product["discount_posttax"])) {
        $v->name("Discount posttax")->nInt($product["discount_posttax"]);
    }

    if (isset($product["discount_pretax"])) {
        $v->name("Discount pretax")->nInt($product["discount_pretax"]);
    }

    if (isset($product["image_url"])) {
        $v->name("Image URL")->str($product["image_url"]);
    }

    if (isset($product["manufacturer"])) {
        $v->name("Manufacturer")->str($product["manufacturer"]);
    }

    if (isset($product["multi_item_price_one"])) {
        $v->name("Multi Item Price One")->nFloat($product["multi_item_price_one"]);
    }

    if (isset($product["multi_item_quantity_one"])) {
        $v->name("Multi Item Quantity One")->nInt($product["multi_item_quantity_one"]);
    }

    if (isset($product["multi_item_price_two"])) {
        $v->name("Multi Item Price Two")->nFloat($product["multi_item_price_two"]);
    }

    if (isset($product["multi_item_quantity_two"])) {
        $v->name("Multi Item Quantity Two")->nInt($product["multi_item_quantity_two"]);
    }

    if (isset($product["multi_item_price_three"])) {
        $v->name("Multi Item Price Three")->nFloat($product["multi_item_price_three"]);
    }

    if (isset($product["multi_item_quantity_three"])) {
        $v->name("Multi Item Quantity Three")->nInt($product["multi_item_quantity_three"]);
    }

    if (isset($product["per_order_limit"])) {
        $v->name("Per Order Limit")->nInt($product["per_order_limit"]);
    }

    if (isset($product["product_mode"])) {
        $v->name("Product Mode")->str($product["product_mode"]);
    }

    if (isset($product["name"])) {
        $v->name("Name")->str($product["name"]);
    }

    if (isset($product["product_status"])) {
        $v->name("Product Status")->str($product["product_status"]);
    }

    if (isset($product["quantity"])) {
        $v->name("Quantity")->nInt($product["quantity"]);
    }

    if (isset($product["regular_price"])) {
        $v->name("Regular Price")->nFloat($product["regular_price"]);
    }

    if (isset($product["sale_type"])) {
        $v->name("Sale Type")->str($product["sale_type"]);
    }

    if (isset($product["selling_price"])) {
        $v->name("Selling Price")->nFloat($product["selling_price"]);
    }

    if (isset($product["size"])) {
        $v->name("Size")->str($product["size"]);
    }

    if (isset($product["sku"])) {
        $v->name("SKU")->nInt($product["sku"]);
    }

    if (isset($product["special_value"])) {
        $v->name("Special Value")->nFloat($product["special_value"]);
    }

    if (isset($product["stock_quantity"])) {
        $v->name("Stock quantity")->nInt($product["stock_quantity"]);
    }

    if (isset($product["tax_status"])) {
        $v->name("Tax Status")->str($product["tax_status"]);
    }

    if (isset($product["tax_value"])) {
        $v->name("Tax Value")->nFloat($product["tax_value"]);
    }

    if (isset($product["vendor"])) {
        $v->name("Vendor")->str($product["vendor"]);
    }

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }
}

/**
 * Create store temp product handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function createStoreTempProductV1($ctx, $args)
{
    $payload = payload();
    $payload["upc"] = arrVal($payload, "upc");
    $payload["price"] = arrVal($payload, "price");
    // $payload["upc_status_request"] = arrVal($payload, "upc_status_request",1);
    validateTempProduct($payload);

    $payload["admin_id"] = $ctx->tokenData->store_admin_id;
    $payload["created_on"] = $ctx->now;
    $payload["updated_on"] = $ctx->now;

    $productCatalogueModel = new ProductCatalogue;
    $upcExistsInCatalogue = $productCatalogueModel->upcExists($ctx, $payload["upc"]);

    if ($upcExistsInCatalogue) {
        $storeProductModel = new StoreProduct($ctx->tokenData->store_admin_id);
        $upcExistsInStore = $storeProductModel->upcExists($ctx, $payload["upc"]);

        if (!$upcExistsInStore) {
            $productFromCatalogue = $productCatalogueModel->getInfoByUPC($ctx, $payload["upc"]);

            $payload["product_id"]          = $productFromCatalogue["product_id"];
            $payload["name"]                = arrVal($payload, "name", $productFromCatalogue["name"]);
            $payload["category_id"]         = arrVal($payload, "category_id", $productFromCatalogue["category_id"]);
            $payload["subcategory_id"]      = arrVal($payload, "subcategory_id", $productFromCatalogue["subcategory_id"]);
            $payload["brand"]               = arrVal($payload, "brand", $productFromCatalogue["brand"]);
            $payload["manufacturer"]        = arrVal($payload, "manufacturer", $productFromCatalogue["manufacturer"]);
            $payload["vendor"]              = arrVal($payload, "vendor", $productFromCatalogue["vendor"]);
            $payload["description"]         = arrVal($payload, "description", $productFromCatalogue["description"]);
            $payload["pos_description"]     = arrVal($payload, "pos_description", $productFromCatalogue["pos_description"]);
            $payload["status"]              = arrVal($payload, "status", $productFromCatalogue["status"]);
            $payload["product_status"]      = arrVal($payload, "product_status", $productFromCatalogue["product_status"]);
            $payload["color"]               = arrVal($payload, "color", $productFromCatalogue["color"]);
            $payload["size"]                = arrVal($payload, "size", $productFromCatalogue["size"]);
            $payload["sku"]                 = arrVal($payload, "sku", $productFromCatalogue["sku"]);
            $payload["quantity"]            = arrVal($payload, "quantity", $productFromCatalogue["quantity"]);
            $payload["stock_quantity"]      = arrVal($payload, "stock_quantity", $productFromCatalogue["stock_quantity"]);
            $payload["age_restriction"]     = arrVal($payload, "age_restriction", $productFromCatalogue["age_restriction"]);
            $payload["sale_type"]           = arrVal($payload, "sale_type", $productFromCatalogue["sale_type"]);
            $payload["per_order_limit"]     = arrVal($payload, "per_order_limit", $productFromCatalogue["per_order_limit"]);
            $payload["product_mode"]        = arrVal($payload, "product_mode", $productFromCatalogue["product_mode"]);

            $insertID = $productCatalogueModel->create($ctx, $payload);

            if (!$insertID) {
                sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR, "Failed to create product");
            }
        }

        $product = $storeProductModel->getInfoByUPC($ctx, $payload["upc"]);
        $category = (new Category)->getInfoByID($ctx, $product["category_id"]);
        $subcategory = (new Subcategory)->getInfoByID($ctx, $product["subcategory_id"]);
        sendJSON(200, [
            "product"       => $product,
            "category"      => $category ? $category : NULL,
            "subcategory"   => $subcategory ? $subcategory : NULL,
        ]);
    } else {
        $tempProductModel = new StoreTempProduct($ctx->tokenData->store_admin_id);
        $upcExistsInTemp = $tempProductModel->upcExists($ctx, $payload["upc"]);

        if (!$upcExistsInTemp) {
            $productID = $productCatalogueModel->generateProductID($ctx);
            $insertID = $tempProductModel->create($ctx, [
                "name"                  => arrVal($payload, "name"),
                "product_id"            => $productID,
                "description"           => arrVal($payload, "description"),
                "price"                 => arrVal($payload, "price"),
                "selling_price"         => arrVal($payload, "selling_price"),
                "color"                 => arrVal($payload, "color"),
                "size"                  => arrVal($payload, "size"),
                "product_status"        => arrVal($payload, "product_status"),
                "quantity"              => arrVal($payload, "quantity"),
                "per_order_limit"       => arrVal($payload, "per_order_limit"),
                "upc"                   => arrVal($payload, "upc"),
                "regular_price"         => arrVal($payload, "regular_price"),
                "buying_price"          => arrVal($payload, "buying_price"),
                "tax_status"            => arrVal($payload, "tax_status"),
                "tax_value"             => arrVal($payload, "tax_value"),
                "special_value"         => arrVal($payload, "special_value"),
                "category_id"           => arrVal($payload, "category_id"),
                "subcategory_id"        => arrVal($payload, "subcategory_id"),
                "sku"                   => arrVal($payload, "sku"),
                "image_url"             => arrVal($payload, "image_url"),
                "stock_quantity"        => arrVal($payload, "stock_quantity"),
                "manufacturer"          => arrVal($payload, "manufacturer"),
                "brand"                 => arrVal($payload, "brand"),
                "vendor"                => arrVal($payload, "vendor"),
                "product_mode"          => arrVal($payload, "product_mode"),
                "age_restriction"       => arrVal($payload, "age_restriction"),
                "sale_type"             => arrVal($payload, "sale_type"),
                "created_on"            => $ctx->now,
                "updated_on"            => $ctx->now,
                // "upc_status_request"    => arrVal($payload, "upc_status_request"),
                "admin_id"              => $ctx->tokenData->store_admin_id,
                "multi_item_qty_one"    => arrVal($payload, "multi_item_qty_one"),
                "multi_item_price_one"  => arrVal($payload, "multi_item_price_one"),
                "multi_item_qty_two"    => arrVal($payload, "multi_item_qty_two"),
                "multi_item_price_two"  => arrVal($payload, "multi_item_price_two"),
                "multi_item_qty_three"  => arrVal($payload, "multi_item_qty_three"),
                "multi_item_price_three"=> arrVal($payload, "multi_item_price_three"),
                "discount_percent"      => arrVal($payload, "discount_percent", 0),
                "discount_pretax"       => arrVal($payload, "discount_pretax", 0),
                "discount_posttax"      => arrVal($payload, "discount_posttax", 0),
            ]);

            if (!$insertID) {
                sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR, "Failed to create temp product");
            }
        }

        $product = $tempProductModel->getInfoByUPC($ctx, $payload["upc"]);
        $category = ($product["category_id"])
            ? (new Category)->getInfoByID($ctx, $product["category_id"])
            : NULL;
        $subcategory = ($product["subcategory_id"])
            ? (new Subcategory)->getInfoByID($ctx, $product["subcategory_id"])
            : NULL;
        sendJSON(200, [
            "product"       => $product,
            "category"      => $category,
            "subcategory"   => $subcategory,
        ]);
    }
}

/**
 * Upload product image handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function uploadProductImageV1($ctx, $args)
{
    Logger::infoMsg("File info: " . json_encode($_FILES));
    $uploader = new Uploader("product_image");
    $newFileName = $uploader->upload(PRODUCTS_UPLOADS_DIR);
    if (!$newFileName) {
        sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR, "Failed to upload image");
    }

    Logger::infoMsg("File uploaded as {$newFileName}");
    sendJSON(200, [
        "image_url" => URL_PRODUCT_IMAGES . "/" . $newFileName
    ]);
}

/**
 * Create temp2 product handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function createTemp2ProductV1($ctx, $args)
{
    $payload = payload();

    $v = new Validator;
    $v->name("UPC")->str($payload["upc"])->reqStr();
    $v->name("Price")->nFloat($payload["price"]);
    $v->name("Mobile number")->str($payload["phone"])->reqStr();
    $v->name("Email")->str($payload["email"])->reqStr()->strEmail();
    $v->name("Checkbit")->nInt($payload["checkbit"]);

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $payload["category_id"] = 0;
    $payload["subcategory_id"] = 0;

    if ($payload["category"]) {
        $category = (new Category)->getInfoByString($ctx, $payload["category"]);

        $payload["category_id"] = isset($category) ? $category["id"] : 0;
        if (!$category) {
           $craeteCategory = (new Category)->create($ctx, $payload["category"]);

           $payload["category_id"] = $craeteCategory;
        }
    }

    if ($payload["subcategory"]) {
        $subCategory = (new Subcategory)->getInfoByString($ctx, $payload["subcategory"]);

        $payload["subcategory_id"] = isset($subCategory) ? $subCategory["id"] : 0;
        if (!$subCategory) {
            $data = [
                "subcategory" => $payload["subcategory"],
                "cat_id"      => $payload["category_id"]
            ];
           $subCategory = (new Subcategory)->create($ctx, $data);

           $payload["subcategory_id"] = $subCategory;
        }
    }
    $payload["category_id"]  = ($payload["category_id"] === 0)
            ? (int) 60
            : (int) ($payload["category_id"]); //byDefault
    $payload["subcategory_id"]  = ($payload["subcategory_id"] === 0)
            ? (int) 1086 //byDefault subcategory_id.
            : (int) ($payload["subcategory_id"]);

    $payload["latitude"] = isset($payload["latitude"]) ? $payload["latitude"] : NULL;
    $payload["longitude"] = isset($payload["longitude"]) ? $payload["longitude"] : NULL;
    $payload["localtime"] = isset($payload["localtime"]) ? $payload["localtime"] : NULL;
    $payload["weather"] = NULL;
    $payload["discount_percent"]  = isset($payload["discount_percent"]) ? $payload["discount_percent"] : 0;
    $payload["discount_pretax"]   = isset($payload["discount_pretax"]) ? $payload["discount_percent"] : 0;
    $payload["discount_posttax"]  = isset($payload["discount_posttax"]) ? $payload["discount_percent"] : 0;
   /* $payload["category_id"]  = 60; //byDefault
    $payload["subcategory_id"]  = 1086; //byDefault category_type is equal to subcategory_id.*/

    if ($payload["latitude"] && $payload["longitude"]) {
        $payload["weather"] = Weather::getUpdate($payload["latitude"], $payload["longitude"]);
    }

    // $ctx->db->beginTransaction();
    $tempProduct2Handler = new StoreTempProduct2($ctx->tokenData->store_admin_id);
    // $upcExistsInTemp2 = $tempProduct2Handler->upcExists($ctx, $payload["upc"]);

    // if (!$upcExistsInTemp2) {
        $payload["product_id"]  = (new ProductCatalogue)->generateProductID($ctx);
        $id = $tempProduct2Handler->create($ctx, $payload);
    // } else {
    //     $upcExistsInTemp2 = $tempProduct2Handler->getInfoByUPC($ctx, $payload["upc"]);
    //     $payload["product_id"] = $upcExistsInTemp2["product_id"];
    //     $id = $tempProduct2Handler->updateProduct($ctx, $payload);
    // }

    if (!$id) {
        $ctx->db->rollback();
        sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR, "Failed to create temp product 2");
    }

    $product = $tempProduct2Handler->getInfoByUPC($ctx, $payload["upc"]);
    
    sendJSON(200, [
        "product"       => $product
    ]);

    // Store in catlogue.
    // Store in store products.
}

/**
 * Update store Temp product handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function updateStoreTempProductV1($ctx, $args)
{
    $payload = payload();
    $input = [
        "stock_quantity" => arrVal($payload, "stock_update"),
        "name" => arrVal($payload, "name"),
        "regular_price" => arrVal($payload, "regular_price"),
    ];

    // Validate input.
    $v = new Validator;
    $v->name("Product ID")->str($args["product_id"])->reqStr();

    if (isset($payload["stock_quantity"])) {
        $v->name("Stock quantity")->nInt($payload["stock_quantity"]);
    }

    if (isset($payload["regular_price"])) {
        $v->name("Regular Price")->nFloat($payload["regular_price"]);
    }

    if (isset($payload["name"])) {
        $v->name("name")->str($payload["name"])->reqStr();
    }

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    // Fetch product info.
    $storeTempProductModel = new StoreTempProduct($ctx->tokenData->store_admin_id);
    $product = $storeTempProductModel->getInfoByProductID($ctx, $args["product_id"]);

    if (!$product['id']) {
        sendErrJSON(404, ERR_PRODUCT_NOT_FOUND);
    }

    // Update info.
    $data["product_id"] = $args["product_id"];
    $data['stockQty'] = empty($input["stock_quantity"]) ? 0 + $product["stock_quantity"] : $input["stock_quantity"] + $product["stock_quantity"];
    $data["name"] = empty($input["name"]) ? $product["name"] : $input["name"];
    $data['regularPrice'] = empty($input["regular_price"]) ? $product["regular_price"] : $input["regular_price"];
    $affectedRows = $storeTempProductModel->updateDescription($ctx, $data);

    if ($affectedRows < 1) {
        sendErrJSON(500, ERR_UPDATE_PRODUCT_FAILED);
    }

    sendJSON(200, $storeTempProductModel->getInfoByProductID($ctx, $args["product_id"]));
}

/**
 * Update store Temp product price handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function updateStoreTempProductPriceV1($ctx, $args)
{
    $payload = payload();
    $input = [
        "regular_price"         => arrVal($payload, "regular_price"),
        "multi_item_qty_one"    => arrVal($payload, "multi_item_qty_one"),
        "multi_item_price_one"  => arrVal($payload, "multi_item_price_one"),
        "multi_item_qty_two"    => arrVal($payload, "multi_item_qty_two"),
        "multi_item_price_two"  => arrVal($payload, "multi_item_price_two"),
        "multi_item_qty_three"  => arrVal($payload, "multi_item_qty_three"),
        "multi_item_price_three"=> arrVal($payload, "multi_item_price_three"),
        "discount_percent"      => arrVal($payload, "discount_percent", 0),
        "discount_pretax"       => arrVal($payload, "discount_pretax", 0),
        "discount_posttax"      => arrVal($payload, "discount_posttax", 0),
        "stock_quantity"        => arrVal($payload, "stock_update"),
        "buying_price"          => arrVal($payload, "buying_price"),
        "tax_status"            => arrVal($payload, "tax_status"),
        "tax_value"             => arrVal($payload, "tax_value"),
    ];

    // Validate input.
    $v = new Validator;
    $v->name("Product ID")->str($args["product_id"])->reqStr();
    $v->name("Regular Price")->nFloat($input["regular_price"]);
    $v->name("Multi Item Quantity One")->nInt($input["multi_item_qty_one"]);
    $v->name("Multi Item Price One")->nFloat($input["multi_item_price_one"]);
    $v->name("Multi Item Quantity Two")->nInt($input["multi_item_qty_two"]);
    $v->name("Multi Item Price Two")->nFloat($input["multi_item_price_two"]);
    $v->name("Multi Item Quantity Three")->nInt($input["multi_item_qty_three"]);
    $v->name("Multi Item Price Three")->nFloat($input["multi_item_price_three"]);
    $v->name("Discount Percent")->nInt($input["discount_percent"]);
    $v->name("Discount Pretax")->nInt($input["discount_pretax"]);
    $v->name("Discount Posttax")->nInt($input["discount_posttax"]);

    if (isset($payload["stock_quantity"])) {
        $v->name("Stock Quantity")->nInt($payload["stock_quantity"]);
    }
    
    $v->name("Buying Price")->nFloat($input["buying_price"]);

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    if (!empty($input["tax_status"])) {
        $v->name("Tax Status")->str($input["tax_status"])->reqStr();
    }

    // Fetch product info.
    $storeTempProductModel = new StoreTempProduct($ctx->tokenData->store_admin_id);
    $product = $storeTempProductModel->getInfoByProductID($ctx, $args["product_id"]);

    if (!$product['id']) {
        sendErrJSON(404, ERR_PRODUCT_NOT_FOUND);
    }

    $changed = FALSE;
    $changed = ((float) $product["regular_price"] !== (float) $input["regular_price"]) || $changed;
    $changed = ((int) $product["multi_item_qty_one"] !== (int) $input["multi_item_qty_one"]) || $changed;
    $changed = ((float) $product["multi_item_price_one"] !== (float) $input["multi_item_price_one"]) || $changed;
    $changed = ((int) $product["multi_item_qty_two"] !== (int) $input["multi_item_qty_two"]) || $changed;
    $changed = ((float) $product["multi_item_price_two"] !== (float) $input["multi_item_price_two"]) || $changed;
    $changed = ((int) $product["multi_item_qty_three"] !== (int) $input["multi_item_qty_three"]) || $changed;
    $changed = ((float) $product["multi_item_price_three"] !== (float) $input["multi_item_price_three"]) || $changed;
    $changed = ((int) $product["discount_percent"] !== (int) $input["discount_percent"]) || $changed;
    $changed = ((int) $product["discount_pretax"] !== (int) $input["discount_pretax"]) || $changed;
    $changed = ((int) $product["discount_posttax"] !== (int) $input["discount_posttax"]) || $changed;
    $changed = ((int) $product["stock_quantity"] !== (int) $input["stock_quantity"]) || $changed;
    $changed = ((float) $product["buying_price"] !== (float) $input["buying_price"]) || $changed;

    $priceInfo = new stdClass;
    $priceInfo->regular_price = (float) $input["regular_price"];
    $priceInfo->multi_item_qty_one = (int) $input["multi_item_qty_one"];
    $priceInfo->multi_item_price_one = (float) $input["multi_item_price_one"];
    $priceInfo->multi_item_qty_two = (int) $input["multi_item_qty_two"];
    $priceInfo->multi_item_price_two = (float) $input["multi_item_price_two"];
    $priceInfo->multi_item_qty_three = (int) $input["multi_item_qty_three"];
    $priceInfo->multi_item_price_three = (float) $input["multi_item_price_three"];
    $priceInfo->discount_percent = (int) $input["discount_percent"];
    $priceInfo->discount_pretax = (int) $input["discount_pretax"];
    $priceInfo->discount_posttax = (int) $input["discount_posttax"];
    $priceInfo->stock_quantity = (int) empty($input["stock_quantity"]) ? 0 + $product["stock_quantity"] : $input["stock_quantity"] + $product["stock_quantity"];
    $priceInfo->buying_price = (float) $input["buying_price"];
    $priceInfo->tax_value = (string) $input["tax_value"];
    $priceInfo->tax_status = (string) $input["tax_status"];

    $affectedRows = $storeTempProductModel->updatePrice($ctx, $args["product_id"], $priceInfo);

    if ($affectedRows < 1) {
        sendErrJSON(500, ERR_UPDATE_PRODUCT_FAILED);
    }

    sendJSON(200, $storeTempProductModel->getInfoByProductID($ctx, $args["product_id"]));
}

/**
 * Coping one store_admin products to another store_admin Exclude MY_STORE Category Products.
 * @param object $ctx Context.
 */
function copyStoreProduct($ctx)
{
    $payload = payload();
    $input = [
        "from_store_id" => arrVal($payload, "from_store_id"),
        "to_store_id" => arrVal($payload, "to_store_id"),
    ];

    // Validate input.
    $v = new Validator;

    if (isset($payload["from_store_id"])) {
        $v->name("From Store Id")->nInt($payload["from_store_id"]);
    }

    if (isset($payload["to_store_id"])) {
        $v->name("To Store Id")->nInt($payload["to_store_id"]);
    }

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }
    // Fetch product from store.
    $storeProductModel = new StoreProduct($input["from_store_id"]);//From store admin id
    $storeProduct = $storeProductModel->getStoreProduct($ctx);

    foreach ($storeProduct as $key => $value) {
        $value["admin_id"]               = $input["to_store_id"]; //To store admin id
        $value["Date_Created"]           = $ctx->now;
        $value["created_date_on"]        = $ctx->now;
        $value["created_on"]             = $ctx->now;
        $value["updated_on"]             = $ctx->now;

        // Fetch product from catalogue.
        $productModel = new ProductCatalogue;
        $insertID = $productModel->createDetails($ctx, $value);
    }
   
    $storeProductModel1 = new StoreProduct($input["to_store_id"]);
    $storeProduct = $storeProductModel1->getAll($ctx);
    sendJSON(201, [
        "product"       => $storeProduct,
    ]);
}

/**
 * Delete store product handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function deleteProductV1($ctx, $args)
{
    $payload = payload();

    $input = [
        "product_type" => arrVal($payload, "product_type")
    ];

    // Validate input.
    $v = new Validator;

    if (isset($payload["product_type"])) {
        $v->name("Product Type")->str($payload["product_type"])->reqStr();
    }

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    // Fetch product info.
    $storeProductModel = new StoreProduct($ctx->tokenData->store_admin_id);
    $storeTempProductModel = new StoreTempProduct($ctx->tokenData->store_admin_id);

    $product = $storeProductModel->productExists($ctx, $args["product_id"]);
    $tempProduct = $storeTempProductModel->productExists($ctx, $args["product_id"]);

    // Delete product.
    $data["product_status"] = "InActive";
    $data["product_id"] = $args["product_id"];
    $affectedRows = "";

    if ($product && $input["product_type"] === "STORE-PRODUCT") {

        $affectedRows = $storeProductModel->deleteProduct($ctx, $data);
    } else if ($tempProduct && $input["product_type"] === "STORE-TEMP-PRODUCT") {
       $affectedRows = $storeTempProductModel->deleteProduct($ctx, $data);
    } else{
        sendErrJSON(404, ERR_PRODUCT_NOT_FOUND);
    }

    if ($affectedRows == 1) {
        sendJSON(200, ["message" => "Product Deleted Successfully"]);
    } else {
        sendErrJSON(404, ERR_PRODUCT_NOT_FOUND);
    }
}
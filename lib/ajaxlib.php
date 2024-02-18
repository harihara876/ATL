<?php

require_once("../init/init.php");

use Plat4m\App\DB;
use Plat4m\Core\Web\Product;

session_start();

$draw = 0; // Quick FIX: Setting a default value.

try {
    $db = (new DB)->getConn();
    $productHandler = (new Product($db))->setStoreAdminID($_SESSION["id"]);

    // Read value.
    $draw               = $_POST["draw"];
    $row                = $_POST["start"];
    $rowsPerPage        = $_POST["length"]; // Rows per page to display.
    $columnIndex        = $_POST["order"][0]["column"]; // Column index.
    $columnName         = $_POST["columns"][$columnIndex]["data"]; // Column name.
    $columnSortOrder    = $_POST["order"][0]["dir"]; // asc or desc.
    $searchValue        = $_POST["search"]["value"]; // Search value.
    
    $totalRecords = $productHandler->totalCount();
    
    $totalRecordswithFilter = $productHandler->totalCountWithFilter([
        "productName"   => $searchValue,
        "upc"           => $searchValue,
    ]);
    
    $products = $productHandler->productsWithFilter([
        "productName"       => $searchValue,
        "upc"               => $searchValue,
        "columnName"        => $columnName,
        "columnSortOrder"   => $columnSortOrder,
        "offset"            => $row,
        "rowCount"          => $rowsPerPage,
    ]);

    foreach ($products as &$product) {
        $encodedID = base64_encode($product["id"]);
        $product["image"] = (!empty($product["image"])) ? "<img src='{$product['image']}' height='100' width='80'>" : NULL;
        $product["Action"] = "<a href=\"viewproductsdetails.php?id='{$encodedID}'\"><button class='btn btn-primary'>View <i class='fa fa-eye' aria-hidden='true'></i></button></a>";
        $product["Manage"] = "<a href=\"editproductsdetails.php?id='{$encodedID}'\"><button class='btn btn-primary  btn-success'>Edit <i class='fa fa-pencil' aria-hidden='true'></i></button></a> &nbsp; <a href=\"deleteproducts.php?id='{$encodedID}'\" class='btn btn-social-icon btn-google' onClick='return checkDelete()'><i class='fa fa fa-trash-o'></i></a>";
    }

    header("Content-Type: application/json");
    echo json_encode([
        "draw"                  => intval($draw),
        "iTotalRecords"         => $totalRecords,
        "iTotalDisplayRecords"  => $totalRecordswithFilter,
        "aaData"                => $products
    ]);
} catch (Exception $ex) {
    http_response_code($ex->getCode());
    header("Content-Type: application/json");
    echo json_encode([
        "draw"                  => intval($draw),
        "iTotalRecords"         => 0,
        "iTotalDisplayRecords"  => 0,
        "aaData"                => []
    ]);
}

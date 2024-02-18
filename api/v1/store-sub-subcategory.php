<?php

require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Core\API\SubSubcategory;
use Plat4m\Utilities\Logger;
use Plat4m\Utilities\Request;
use Plat4m\Utilities\Response;

try {
    Middleware::verifyAuth();

    // Get input params.
    $payload = Request::payload();
    Logger::httpMsg($payload);

    if (empty($payload["storeid"])) {
        throw new Exception("Store ID is required", 400);
    }

    $db = (new DB)->getConn();
    $subsubcategoryHandler = new SubSubcategory($db);
    $subsubcategories = $subsubcategoryHandler->getAllByStoreAdminID($payload["storeid"]);
    Logger::infoMsg(sprintf("Returned subsubcategories: %d", count($subsubcategories)));

    if (count($subsubcategories) == 0) {
        throw new Exception("No subsubcategories found", 404);
    }

    Response::statusCode(200)::body([
        "output" => $subsubcategories
    ])::json();
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body(["error" => $ex->getMessage()])::json();
}

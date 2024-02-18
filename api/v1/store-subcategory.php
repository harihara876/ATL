<?php

require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Core\API\Subcategory;
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
    $subcategoryHandler = new Subcategory($db);
    $subcategories = $subcategoryHandler->getAllByStoreAdminID($payload["storeid"]);
    Logger::infoMsg(sprintf("Returned subcategories: %d", count($subcategories)));

    if (count($subcategories) == 0) {
        throw new Exception("No subcategories found", 404);
    }

    Response::statusCode(200)::body([
        "output" => $subcategories
    ])::json();
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body(["error" => $ex->getMessage()])::json();
}

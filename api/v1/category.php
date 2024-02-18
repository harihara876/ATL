<?php

require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Core\API\Category;
use Plat4m\Utilities\Logger;
use Plat4m\Utilities\Response;

try {
    Middleware::verifyAuth();

    $db = (new DB)->getConn();
    $categoryHandler = new Category($db);
    $categories = $categoryHandler->getAll();
    Logger::infoMsg(sprintf("Returned categories: %d", count($categories)));

    if (count($categories) == 0) {
        throw new Exception("No categories found", 404);
    }

    Response::statusCode(200)::body([
        "output" => $categories
    ])::json();
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body(["error" => $ex->getMessage()])::json();
}

<?php

require_once("../../init/init.php");

use Plat4m\App\Middleware;
use Plat4m\Utilities\Logger;
use Plat4m\Utilities\Response;
use Plat4m\Utilities\Uploader;

try {
    Middleware::verifyAuth();

    Logger::httpMsg();
    Logger::infoMsg("File info: " . json_encode($_FILES));
    $uploader = new Uploader("product_image");
    $newFileName = $uploader->upload(PRODUCTS_UPLOADS_DIR);
    if (!$newFileName) {
        throw new Exception("Failed to upload", 500);
    }

    Logger::infoMsg("File uploaded as {$newFileName}");
    Response::statusCode(200)::body([
        "image_url" => URL_PRODUCT_IMAGES . "/" . $newFileName
    ])::json();
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body(["error" => $ex->getMessage()])::json();
}

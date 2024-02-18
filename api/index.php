<?php
use Plat4mAPI\App\DB;
use Plat4mAPI\Util\Logger;

require_once("../init/init.php");
require_once("helpers.php");
require_once("middleware.php");

// Include all handlers.
// TODO: Improvement required. Find a way to dynamically include required files.
foreach (glob("handlers/*.php") as $filename) {
    require_once($filename);
}

define("ROUTES", [
    // TODO: Remove this. Useless.
    ["GET", "/api/v1/auth/logout", "userLogoutV1"],

    //Copy products from one store to another store.
    ["POST", "/api/v3/store/products/copy", "skipAuth|copyStoreProduct"],

    // Verify Email
    ["POST", "/api/v3/user/verify-email", "skipAuth|verifyEmail"],
    ["POST", "/api/v3/user/validate-email-otp", "skipAuth|validateEmailWithOTP"],

    // Admin.
    ["POST", "/api/v3/admin/create", "skipAuth|createAdminV1"],
    ["GET", "/api/v3/admin", "adminOnly|adminProfileV1"],
    ["PATCH", "/api/v3/admin/update", "adminOnly|updateAdminV1"],
    ["POST", "/api/v3/admin/paytm-credentials/update", "adminOnly|updatePaytmCredentailsV1"], //pending for migration
    ["POST", "/api/v3/admin/password/change", "adminOnly|adminPasswordChangeV1"],
    ["GET", "/api/v3/admin/cashiers", "getAllAdminCashiersV1"], //pending for migration
    ["PUT", "/api/v3/admin/cashier/{cashier_id}/delete", "adminOnly|deleteCashier"],
    ["PUT", "/api/v3/admin/store-update", "updateStoreAdminV1"],

    // Reset password for admin or cashier.
    ["POST", "/api/v3/user/password/reset/request", "skipAuth|userResetPasswordRequestV1"],
    ["POST", "/api/v3/user/password/reset", "skipAuth|userResetPasswordV1"],

    // Cashier.
    ["POST", "/api/v3/cashier/create", "adminOnly|createCashierV1"],
    ["GET", "/api/v3/cashier", "cashierOnly|cashierProfileV1"],
    ["PATCH", "/api/v3/cashier/update", "cashierOnly|updateCashierV1"],
    ["POST", "/api/v3/cashier/password/change", "cashierOnly|cashierPasswordChangeV1"],

    // Authentication.
    ["POST", "/api/v3/auth/login", "skipAuth|loginV1"],
    ["GET", "/api/v3/auth/logout", "logoutV1"],
    ["GET", "/api/v3/auth/refresh", "skipAuth|refreshTokensV1"],
    ["POST", "/api/v3/auth/login/force/request", "skipAuth|requestForceLogin"],
    ["POST", "/api/v3/auth/login/force", "skipAuth|forceLogin"],

    // Categories.
    ["GET", "/api/v3/store/categories", "getStoreCategoriesV1"],
    ["GET", "/api/v3/store/subcategories", "getStoreSubcategoriesV1"],
    ["GET", "/api/v3/store/subsubcategories", "getStoreSubSubcategoriesV1"],

    // Products.
    ["GET", "/api/v3/store/products", "getStoreProductsV1"],
    ["GET", "/api/v3/store/products/temp", "getStoreTempProductsV1"],
    ["GET", "/api/v3/store/products/temp2", "getStoreTemp2ProductsV1"],
    ["GET", "/api/v3/store/product/upc/{upc}", "getStoreProductV1"],
    ["GET", "/api/v3/store/product/{product_id}/upc/{upc}/price-range", "getProductPriceRangeV1"],
    ["POST", "/api/v3/store/product/upc/{upc}/copy-from-catalogue", "copyCatalogueProductToStoreV1"],
    ["PUT", "/api/v3/store/product/{product_id}/update", "updateStoreProductV1"],
    ["PUT", "/api/v3/store/product/temp/{product_id}/update", "updateStoreTempProductV1"],
    ["POST", "/api/v3/store/product/{product_id}/price/change", "updateStoreProductPriceV1"],
    ["POST", "/api/v3/store/product/temp/{product_id}/price/change", "updateStoreTempProductPriceV1"],
    ["POST", "/api/v3/store/product/temp/create", "createStoreTempProductV1"],
    ["POST", "/api/v3/store/product-image/upload", "uploadProductImageV1"],
    ["POST", "/api/v3/store/product/temp2/create", "createTemp2ProductV1"],
    ["PUT", "/api/v3/store/product/{product_id}/delete", "deleteProductV1"],
    ["GET", "/api/v3/store/product/temp2/upc/{upc}", "checkUPCV1"],

    // Transactions.
    ["POST", "/api/v3/orders/create", "createOrderV1"],
    ["GET", "/api/v3/order/{order_id:\d+}", "viewOrderV1"],
    ["POST", "/api/v3/order/{order_id:\d+}/status/update", "updateOrderStatusV1"],

    // Reports.
    ["POST", "/api/v3/reports/orders-summary", "transactionsSummaryV1"],
    ["POST", "/api/v3/reports/product-sales", "productSalesV1"],
    ["POST", "/api/v3/reports/transactions", "listTransactionsV1"],
    ["POST", "/api/v3/reports/orders-summary-email", "requestOrderSummaryReportPdf"],
    ["POST", "/api/v3/reports/product-sales-email", "requestProductSalesReportPdf"],
    ["POST", "/api/v3/reports/transactions-email", "requestTransactionReportPdf"],
    ["POST", "/api/v3/reports/receipt-email", "receiptEmail"],
    ["POST", "/api/v3/reports/send-sms", "sendSms"],
    ["POST", "/api/v3/reports/send-whatsApp", "sendWhatsApp"],
]);

/**
 * Create dispatcher, setup routes and dispatch requests.
 */
function run()
{
    // Create dispatcher and register routes.
    $dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
        foreach (ROUTES as $route) {
            $r->addRoute($route[0], $route[1], $route[2]); // Method, path, actions.
        }
    });
    // Read request HTTP method and URI.
    $httpMethod = $_SERVER["REQUEST_METHOD"];
    $uri = $_SERVER["REQUEST_URI"];

    // Strip query string (?foo=bar) and decode URI.
    if (false !== $pos = strpos($uri, '?')) {
        $uri = substr($uri, 0, $pos);
    }

    $uri = rawurldecode($uri);
    // Dispatch the request.
    $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
    handleRoute($routeInfo);
}

/**
 * Handles routes.
 * @param array $routeInfo Route info.
 */
function handleRoute($routeInfo)
{
    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            sendErrJSON(404, ERR_RESOURCE_NOT_FOUND);
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $allowedMethods = implode(",", $routeInfo[1]);
            header("Allow: {$allowedMethods}");
            sendErrJSON(405, ERR_METHOD_NOT_ALLOWED);
            break;
        case FastRoute\Dispatcher::FOUND:
            invokeHandler($routeInfo);
            break;
        default:
            sendErrJSON(404, ERR_RESOURCE_NOT_FOUND, "Unknown error");
            die;
    }
}

/**
 * Invoke handler.
 * @param array $routeInfo Route info.
 */
function invokeHandler($routeInfo)
{

    $handlers = explode("|", $routeInfo[1]);
    $action = $handlers[count($handlers) - 1];
    $vars = $routeInfo[2];
    $tokenData = NULL;

    // Logger::httpMsg(getallheaders());

    // Verify user auth unless "skipAuth" is mentioned.
    if (!in_array("skipAuth", $handlers)) {
        $tokenData = verifyAuth();
    }

    try {
        // Create context object.
        $ctx = new stdClass;
        $ctx->clientApp = clientAppInfo();
        $ctx->db = (new DB)->getConn();
        $ctx->now = date(DEFAULT_DATETIME_FMT);
        $ctx->tokenData = $tokenData;
         // print_r($ctx);exit();

        // Verify app name.
        if (!in_array($ctx->clientApp->name, PLAT4M_APPS)) {
            sendErrJSON(400, ERR_BAD_REQUEST, "Unknown app name in 'X-Plat4m-App-Name' header");
        }

        // Verify app instance ID.
        if (empty($ctx->clientApp->instanceID)) {
            sendErrJSON(400, ERR_BAD_REQUEST, "Missing header 'X-Plat4m-App-Instance-Id'");
        }

        // Verify if admin only access.
        if (in_array("adminOnly", $handlers)) {
            $permitted = adminOnlyAccess($ctx);
            if (!$permitted) {
                Logger::infoMsg("Forbidden");
                sendErrJSON(403, ERR_FORBIDDEN, "Not allowed to access");
            }
        }

        // Verify if cashier only access.
        if (in_array("cashierOnly", $handlers)) {
            $permitted = cashierOnlyAccess($ctx);
            if (!$permitted) {
                Logger::infoMsg("Forbidden");
                sendErrJSON(403, ERR_FORBIDDEN, "Not allowed to access");
            }
        }
        
        // Verify active device.
        if ($ctx->tokenData) {
            $verifyDevice = verifyDevice($ctx);
        }
        
        // Invoke handler.
        $action($ctx, $vars);
    } catch (Exception $ex) {
        Logger::errExcept($ex);

        // If DB connection is a transaction, rollback.
        if ($ctx->db->inTransaction()) {
            $ctx->db->rollBack();
        }

        sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR);
    }
}

// Run the app.
run();

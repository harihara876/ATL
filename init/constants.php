<?php

// This file contains necessary constants for the entire application.

// Project paths.
define("PROJECT_DIR", dirname(__DIR__));
define("LOGS_DIR", "/var/log/plat4m/logs");
// define("LOGS_DIR", "/xampp/log");
define("ERROR_LOGS_DIR", "/var/log/plat4m/errors");
// define("ERROR_LOGS_DIR", "/xampp/log");
define("UPLOADS_DIR", PROJECT_DIR . "/uploads");
define("PRODUCTS_UPLOADS_DIR", UPLOADS_DIR . "/products");

// Plat4m apps.
define("PLAT4M_PLAY_STORE_APP", "com.plat4minc.mystore");
define("PLAT4M_IND_PLAY_STORE_APP", "com.plat4minc.mystore.ind");
define("PLAT4M_US_PLAY_STORE_APP", "com.plat4minc.mystore.usa");
define("PLAT4M_CLOVER_STORE_APP", "com.plat4minc.mystore.clover");
define("PLAT4M_OBCR_APP", "OBCR");
define("PLAT4M_APPS", [
    PLAT4M_PLAY_STORE_APP,
    PLAT4M_IND_PLAY_STORE_APP,
    PLAT4M_US_PLAY_STORE_APP,
    PLAT4M_CLOVER_STORE_APP,
    PLAT4M_OBCR_APP
]);

// Order status constants.
define("ORDER_COMPLETED", "Complete");
define("ORDER_IN_PROCESSING", "In-Processing");
define("ORDER_CANCELLED", "Cancel");
define("ORDER_STATUS", [
    ORDER_COMPLETED,
    ORDER_IN_PROCESSING,
    ORDER_CANCELLED,
]);

// User types.
define("USER_ADMIN", "storeadmin");
define("USER_CASHIER", "cashier");

// Currency codes.
define("CURRENCY_CODES", [
    "INDIA" => ["INR", "₹"],
    "USA"   => ["USD", "$"],
]);

// Currency
define("DEFAULT_CURRENCY", "USD");
define("DEFAULT_CURRENCY_SYMBOL", "$");

// Order payment mode.
define("ORDER_PAYMENT_MODE_CASH", "cash");
define("ORDER_PAYMENT_MODE_CARD", "card");
define("ORDER_PAYMENT_MODE_PAYTM", "paytm");

// Order payment status.
define("ORDER_PAYMENT_COMPLETED", "Completed");

// Parameters.
define("RESET_PASSWORD_OTP_LIFE_SPAN", 60); // Minutes.
define("FORCE_LOGIN_OTP_LIFE_SPAN", 60); // Minutes.
define("VERIFY_EMAIL_OTP_LIFE_SPAN", 60); // Minutes.

// URLs.
define("URL_PRODUCT_IMAGES", URL_HOST . "/" . "uploads/products");

// Timezone.
define("SERVER_TIMEZONE_ID", "UTC");
define("SERVER_TIMEZONE_SHORT", "GMT");

// Datetime format.
define("DEFAULT_DATETIME_FMT", "Y-m-d H:i:s");

// Dates and time format sent by mobile.
define("DEFAULT_DATE_INPUT_FMT", "m/d/Y");
define("DEFAULT_TIME_INPUT_FMT", "h:i A");
define("DEFAULT_DATE_INPUT_REGEXP", "/^\d{2}\/\d{2}\/\d{4}$/");
define("DEFAULT_TIME_INPUT_REGEXP", "/^(1[012]|[1-9]):[0-5][0-9](\\s)?(?i)(am|pm)$/");

// Allowed file extensions for upload.
define("ALLOWED_FILE_EXT_FOR_UPLOAD", [
    "csv",
    "doc",
    "gif",
    "jpeg",
    "jpg",
    "png",
    "txt",
    "xls",
    "zip",
]);

// HTTP custom headers.
define("HEADER_APP_NAME", "X-Plat4m-App-Name");
define("HEADER_APP_INSTANCE_ID", "X-Plat4m-App-Instance-Id");
define("HEADER_APP_DEVICE", "X-Plat4m-App-Device");
define("HEADER_APP_VERSION", "X-Plat4m-App-Version");
define("HEADER_APP_PLATFORM", "X-Plat4m-App-Platform");

// HTTP status codes.
define("HTTP_STATUS_OK", 200);
define("HTTP_STATUS_CREATED", 201);
define("HTTP_STATUS_ACCEPTED", 202);
define("HTTP_STATUS_NO_CONTENT", 204);
define("HTTP_STATUS_BAD_REQUEST", 400);
define("HTTP_STATUS_UNAUTHORIZED", 401);
define("HTTP_STATUS_FORBIDDEN", 403);
define("HTTP_STATUS_NOT_FOUND", 404);
define("HTTP_STATUS_INTERNAL_SERVER_ERROR", 500);

define("JWT_TYPE_ACCESS", "access");
define("JWT_TYPE_REFRESH", "refresh");

// Default Values.
define("DEFAULT_CATEGORY", 60);
define("DEFAULT_SUBCATEGORY", 1086);

// Error codes.
define("ERR_UNAUTHORIZED", "err_unauthorized");
define("ERR_BAD_REQUEST", "err_bad_request");
define("ERR_FORBIDDEN", "err_forbidden");
define("ERR_METHOD_NOT_ALLOWED", "err_method_not_allowed");
define("ERR_RESOURCE_NOT_FOUND", "err_resource_not_found");
define("ERR_INTERNAL_SERVER_ERROR", "err_internal_server_error");
define("ERR_VALIDATION", "err_validation");
define("ERR_ACCOUNT_EXISTS", "err_account_exists");
define("ERR_FALSE_ADMIN", "err_false_admin");
define("ERR_FASLE_CASHIER", "err_false_cashier");
define("ERR_CASHIERS_LIMIT_EXCEEDED", "err_cashiers_limit_exceeded");
define("ERR_MAX_LOGINS_REACHED", "err_max_logins_reached");
define("ERR_INVALID_TOKEN", "err_invalid_token");
define("ERR_USER_NOT_FOUND", "err_user_not_found");
define("ERR_ORDER_NOT_FOUND", "err_order_not_found");
define("ERR_UPC_NOT_FOUND", "err_upc_not_found");
define("ERR_PRODUCT_NOT_FOUND", "err_product_not_found");
define("ERR_MISSING_HEADER", "err_missing_header");
define("ERR_INVALID_OTP", "err_invalid_otp");
define("ERR_UPDATE_PRODUCT_FAILED", "err_update_product_failed");
define("ERR_LOGGEDIN_OTHER_DEVICE", "err_loggedin_other_device");
define("ERR_PRODUCT_AND_UPC_NOT_FOUND", "err_product_upc_not_valid");
define("ERR_TRANSACTION_NOT_FOUND", "err_transaction_not_found");
define("ERR_SALES_PRODUCT_NOT_FOUND", "err_sales_product_not_found");

define("ERR_MSG", [
    ERR_UNAUTHORIZED                => "Unauthorized",
    ERR_BAD_REQUEST                 => "Bad request",
    ERR_FORBIDDEN                   => "Forbidden",
    ERR_METHOD_NOT_ALLOWED          => "Method not allowed",
    ERR_RESOURCE_NOT_FOUND          => "Resource not found",
    ERR_INTERNAL_SERVER_ERROR       => "Internal server error",
    ERR_VALIDATION                  => "Validation errors",
    ERR_ACCOUNT_EXISTS              => "Account already exists",
    ERR_FALSE_ADMIN                 => "False admin",
    ERR_FASLE_CASHIER               => "False cashier",
    ERR_CASHIERS_LIMIT_EXCEEDED     => "Cashiers limit exceeded",
    ERR_MAX_LOGINS_REACHED          => "Maximum number of logins reached",
    ERR_INVALID_TOKEN               => "Invalid token",
    ERR_USER_NOT_FOUND              => "User not found",
    ERR_ORDER_NOT_FOUND             => "Order not found",
    ERR_UPC_NOT_FOUND               => "UPC not found",
    ERR_PRODUCT_NOT_FOUND           => "Product not found",
    ERR_MISSING_HEADER              => "Missing header",
    ERR_INVALID_OTP                 => "Invalid OTP",
    ERR_UPDATE_PRODUCT_FAILED       => "Failed to update product",
    ERR_LOGGEDIN_OTHER_DEVICE       => "Session Time Out",
    ERR_PRODUCT_AND_UPC_NOT_FOUND   => "Please enter valid product id and upc",
    ERR_TRANSACTION_NOT_FOUND       => "No Transactions Found during search period",
    ERR_SALES_PRODUCT_NOT_FOUND     => "No Product Found during search period"
]);

// Events.
define("EVENT_LOGIN", "login");
define("EVENT_FORCE_LOGIN", "force-login");
define("EVENT_RESET_PASSWORD", "reset-password");

//Status code for delete cashier
define("DELETE_CASHIER", 2);

define("MYSTORE_CAT_ID", 40);
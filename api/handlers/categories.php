<?php

use Plat4mAPI\Model\Category;
use Plat4mAPI\Model\Subcategory;
use Plat4mAPI\Model\SubSubcategory;
use Plat4mAPI\Util\Logger;

/**
 * Fetch store categories handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function getStoreCategoriesV1($ctx, $args)
{
    $categoryModel = new Category;
    $categories = $categoryModel->getAllByAdminID($ctx, $ctx->tokenData->store_admin_id);
    Logger::infoMsg(sprintf("Returned categories: %d", count($categories)));

    sendJSON(200, ["categories" => $categories], JSON_NUMERIC_CHECK);
}

/**
 * Fetch store subcategories handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function getStoreSubcategoriesV1($ctx, $args)
{
    $subcategoryModel = new Subcategory;
    $subcategories = $subcategoryModel->getAllByAdminID($ctx, $ctx->tokenData->store_admin_id);
    Logger::infoMsg(sprintf("Returned subcategories: %d", count($subcategories)));

    sendJSON(200, ["subcategories" => $subcategories], JSON_NUMERIC_CHECK);
}

/**
 * Fetch store sub-subcategories handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function getStoreSubSubcategoriesV1($ctx, $args)
{
    $subSubcategoryModel = new SubSubcategory;
    $subcategories = $subSubcategoryModel->getAllByAdminID($ctx, $ctx->tokenData->store_admin_id);
    Logger::infoMsg(sprintf("Returned subcategories: %d", count($subcategories)));

    sendJSON(200, ["sub_subcategories" => $subcategories], JSON_NUMERIC_CHECK);
}

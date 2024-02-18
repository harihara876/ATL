<?php

require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Utilities\Helper;
use Plat4m\Core\API\Reports;
use Plat4m\Utilities\Logger;
use Plat4m\Utilities\Request;
use Plat4m\Utilities\Response;

try {
    Middleware::verifyAuth();

    // Get input params.
    $payload = Request::payload();
    Logger::httpMsg($payload);
    $input = [
        "storeid"  => Helper::arrVal($payload, "storeid"),                          // E.g. 26
        "fromDate" => Helper::arrVal($payload, "from_date"),                        // Formatted in m/d/Y
        "toDate"   => Helper::arrVal($payload, "to_date"),                          // Formatted in H:i a
        "fromTime" => Helper::arrVal($payload, "from_time"),                        // Formatted in m/d/Y
        "toTime"   => Helper::arrVal($payload, "to_time"),                          // Formatted in H:i a
        "tzID"     => Helper::arrVal($payload, "tzID", SERVER_TIMEZONE_ID),         // Timezone ID. E.g. Asia/Calcutta
        "tzShort"  => Helper::arrVal($payload, "tzShort", SERVER_TIMEZONE_SHORT),   // Timezone short name. E.g. IST, GMT, PST
    ];
    $datesSet = function (&$input) {
        return (!empty($input["fromDate"]) && !empty($input["toDate"]));
    };
    $timesSet = function (&$input) {
        return (!empty($input["fromTime"]) && !empty($input["toTime"]));
    };

    if ($datesSet($input)) {
        $input["fromDate"] = Helper::convertDateTimeFormat($input["fromDate"], "m/d/Y", "Y-m-d");
        $input["toDate"] = Helper::convertDateTimeFormat($input["toDate"], "m/d/Y", "Y-m-d");
    }

    if ($timesSet($input)) {
        $input["fromTime"] = Helper::convertDateTimeFormat($input["fromTime"], "H:i A", "H:i:s");
        $input["toTime"] = Helper::convertDateTimeFormat($input["toTime"], "H:i A", "H:i:s");
    } else {
        $input["fromTime"] = "00:00:00";
        $input["toTime"] = "23:59:59";
    }

    $from = Helper::convertTimezone(
        "{$input["fromDate"]} {$input["fromTime"]}",
        $input["tzID"],
        SERVER_TIMEZONE_ID
    );
    $to = Helper::convertTimezone(
        "{$input["toDate"]} {$input["toTime"]}",
        $input["tzID"],
        SERVER_TIMEZONE_ID
    );

    // if ($input["toTime"] != "00:00:00") {
    //     $to = Helper::modifyDateTime($to, -1); // 00:00:00 (today) to 23:59:59 (yesterday)
    // }

    Logger::infoMsg("Transactions summary from {$from} to {$to}");

    $db = (new DB)->getConn();

    // If from date and to date are set, fetch summary in that period.
    // Else consider all.
    if ($datesSet($input)) {
        $reportsHandler = (new Reports($db))
            ->setStoreAdminID($input["storeid"])
            ->setFromDate($from)
            ->setToDate($to);
        $summary                = $reportsHandler->totalSellingPriceAndTaxInPeriod();
        $ordersCompleted        = (int) $reportsHandler->ordersCountInPeriod(ORDER_COMPLETED);
        // $ordersInProcessing     = (int) $reportsHandler->ordersCountInPeriod(ORDER_IN_PROCESSING);
        // $ordersCancelled        = (int) $reportsHandler->ordersCountInPeriod(ORDER_CANCELLED);
        // $ordersPaidInCash       = (int) $reportsHandler->ordersCountInPeriodByPaymentMode(ORDER_PAYMENT_MODE_CASH);
        // $ordersPaidWithCard     = (int) $reportsHandler->ordersCountInPeriodByPaymentMode(ORDER_PAYMENT_MODE_CARD);
        $totalSellingPrice      = Helper::formatPrice($summary->sellingPrice);
        $totalTax               = Helper::formatPrice($summary->tax);
        $totalRevenue           = Helper::formatPrice($reportsHandler->totalRevenueInPeriod());
        $totalRevenueCash       = Helper::formatPrice($reportsHandler->totalRevenueByPaymentModeInPeriod(ORDER_PAYMENT_MODE_CASH));
        $totalRevenueCard       = Helper::formatPrice($reportsHandler->totalRevenueByPaymentModeInPeriod(ORDER_PAYMENT_MODE_CARD));
        $totalRevenuePaytm      = Helper::formatPrice($reportsHandler->totalRevenueByPaymentModeInPeriod(ORDER_PAYMENT_MODE_PAYTM));
        $totalLotteryRevenue    = Helper::formatPrice($reportsHandler->totalRevenueByProductLottoInPeriod());
        $totalScratchersRevenue = Helper::formatPrice($reportsHandler->totalRevenueOfScratchersGameInPeriod());
    } else {
        $reportsHandler         = (new Reports($db))->setStoreAdminID($input["storeid"]);
        $summary                = $reportsHandler->totalSellingPriceAndTax();
        $ordersCompleted        = (int) $reportsHandler->ordersCount(ORDER_COMPLETED);
        // $ordersInProcessing     = (int) $reportsHandler->ordersCount(ORDER_IN_PROCESSING);
        // $ordersCancelled        = (int) $reportsHandler->ordersCount(ORDER_CANCELLED);
        // $ordersPaidInCash       = (int) $reportsHandler->ordersCountByPaymentMode(ORDER_PAYMENT_MODE_CASH);
        // $ordersPaidWithCard     = (int) $reportsHandler->ordersCountByPaymentMode(ORDER_PAYMENT_MODE_CARD);
        $totalSellingPrice      = Helper::formatPrice($summary->sellingPrice);
        $totalTax               = Helper::formatPrice($summary->tax);
        $totalRevenue           = Helper::formatPrice($reportsHandler->totalRevenue());
        $totalRevenueCash       = Helper::formatPrice($reportsHandler->totalRevenueByPaymentMode(ORDER_PAYMENT_MODE_CASH));
        $totalRevenueCard       = Helper::formatPrice($reportsHandler->totalRevenueByPaymentMode(ORDER_PAYMENT_MODE_CARD));
        $totalRevenuePaytm      = Helper::formatPrice($reportsHandler->totalRevenueByPaymentMode(ORDER_PAYMENT_MODE_PAYTM));
        $totalLotteryRevenue    = Helper::formatPrice($reportsHandler->totalRevenueByProductLotto());
        $totalScratchersRevenue = Helper::formatPrice($reportsHandler->totalRevenueOfScratchersGame());
    }

    $payload = [
        "order_summary" => [
            "orders_completed"          => $ordersCompleted,
            // "orders_payment_mode_cash"  => $ordersPaidInCash,
            // "orders_payment_mode_card"  => $ordersPaidWithCard,
            "total_selling_price"       => $totalSellingPrice,
            "total_tax"                 => $totalTax,
            "total_revenue"             => $totalRevenue,
            "total_revenue_cash"        => $totalRevenueCash,
            "total_revenue_card"        => $totalRevenueCard,
            "total_revenue_paytm"       => $totalRevenuePaytm,
            "total_lottery_sales"       => $totalLotteryRevenue,
            "total_scratchers_sales"    => $totalScratchersRevenue,
        ]
    ];

    Response::statusCode(200)::body($payload)::json(JSON_PRESERVE_ZERO_FRACTION);
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body(["error" => $ex->getMessage()])::json();
}

<?php
// require_once("../../../vendor/autoload.php");
use Plat4mAPI\Model\Admin;
use Plat4mAPI\Model\RevenueReports;
use Plat4mAPI\Model\SalesReports;
use Plat4mAPI\Model\Transaction;
use Plat4mAPI\Model\TransactionReports;
use Plat4mAPI\Util\Logger;
use Plat4mAPI\Util\Validator;
use Plat4mAPI\Util\Mailer;
use Plat4mAPI\Util\WhatsappAPI;
// use GuzzleHttp\Client;
// use GuzzleHttp\RequestOptions;

/**
 * Transactions summary handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function transactionsSummaryV1($ctx, $args)
{
    $payload = payload();
    $input = [
        "fromDate" => arrVal($payload, "from_date"),                        // Formatted in m/d/Y
        "toDate"   => arrVal($payload, "to_date"),                          // Formatted in H:i a
        "fromTime" => arrVal($payload, "from_time"),                        // Formatted in m/d/Y
        "toTime"   => arrVal($payload, "to_time"),                          // Formatted in H:i a
        "tzID"     => arrVal($payload, "tzID", SERVER_TIMEZONE_ID),         // Timezone ID. E.g. Asia/Calcutta
        "tzShort"  => arrVal($payload, "tzShort", SERVER_TIMEZONE_SHORT),   // Timezone short name. E.g. IST, GMT, PST
    ];

    $v = new Validator;
    $v->name("tzID")->str($input["tzID"])->validTZ();
    $v->name("From date")->str($input["fromDate"])->reqStr()->formatDT(DEFAULT_DATE_INPUT_FMT)->compareDT($input["fromDate"],$input["toDate"]);
    $v->name("To date")->str($input["toDate"])->reqStr()->formatDT(DEFAULT_DATE_INPUT_FMT);

    $datesSet = function (&$input) {
        return (!empty($input["fromDate"]) && !empty($input["toDate"]));
    };
    $timesSet = function (&$input) {
        return (!empty($input["fromTime"]) && !empty($input["toTime"]));
    };

    if ($datesSet($input)) {
        $v->name("From date")->str($input["fromDate"])->reqStr()->formatDT(DEFAULT_DATE_INPUT_FMT);
        $v->name("To date")->str($input["toDate"])->reqStr()->formatDT(DEFAULT_DATE_INPUT_FMT);
        $input["fromDate"] = convertDateTimeFormat($input["fromDate"], "m/d/Y", "Y-m-d");
        $input["toDate"] = convertDateTimeFormat($input["toDate"], "m/d/Y", "Y-m-d");
    }

    if ($timesSet($input)) {
        $v->name("From time")->str($input["fromTime"])->reqStr()->formatDT(DEFAULT_TIME_INPUT_FMT);
        $v->name("To time")->str($input["toTime"])->reqStr()->formatDT(DEFAULT_TIME_INPUT_FMT);
        $input["fromTime"] = convertDateTimeFormat($input["fromTime"], "h:i A", "H:i:s");
        $input["toTime"] = convertDateTimeFormat($input["toTime"], "h:i A", "H:i:s");
    } else {
        $input["fromTime"] = "00:00:00";
        $input["toTime"] = "23:59:59";
    }

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $from = convertTimezone(
        "{$input["fromDate"]} {$input["fromTime"]}",
        $input["tzID"],
        SERVER_TIMEZONE_ID
    );
    $to = convertTimezone(
        "{$input["toDate"]} {$input["toTime"]}",
        $input["tzID"],
        SERVER_TIMEZONE_ID
    );

    Logger::infoMsg("Transactions summary from {$from} to {$to}");

    $filters = new stdClass;
    $filters->storeAdminID = $ctx->tokenData->store_admin_id;
    $filters->fromDate = $from;
    $filters->toDate = $to;
    $filters->orderStatus = ORDER_COMPLETED;
    $filters->paymentMode = ORDER_PAYMENT_MODE_CARD;
    $cashModeFilters = function () use ($filters) {
        $newFilters = clone $filters;
        $newFilters->paymentMode = ORDER_PAYMENT_MODE_CASH;
        return $newFilters;
    };
    $paytmModeFilters = function () use ($filters) {
        $newFilters = clone $filters;
        $newFilters->paymentMode = ORDER_PAYMENT_MODE_PAYTM;
        return $newFilters;
    };

    $revenueReportsModel = new RevenueReports;
    $transactionReportsModel = new TransactionReports;

    if ($datesSet($input)) {
        $summary                = $revenueReportsModel->totalSellingPriceAndTaxInPeriod($ctx, $filters);
        $ordersCompleted        = (int) $transactionReportsModel->ordersCountInPeriod($ctx, $filters);
        $ordersSpecialFee       = $transactionReportsModel->ordersSpecialFeeInPeriod($ctx, $filters);
        $totalSellingPrice      = formatPrice($summary->sellingPrice);
        $totalTax               = formatPrice($summary->tax);
        $totalRevenue           = formatPrice($revenueReportsModel->totalRevenueInPeriod($ctx, $filters));
        $totalRevenueCard       = formatPrice($revenueReportsModel->totalRevenueByPaymentModeInPeriod($ctx, $filters));
        $totalRevenueCash       = formatPrice($revenueReportsModel->totalRevenueByPaymentModeInPeriod($ctx, $cashModeFilters()));
        $totalRevenuePaytm      = formatPrice($revenueReportsModel->totalRevenueByPaymentModeInPeriod($ctx, $paytmModeFilters()));
        // $totalLotteryRevenue    = formatPrice($revenueReportsModel->totalRevenueByProductLottoInPeriod($ctx, $filters));
        // $totalScratchersRevenue = formatPrice($revenueReportsModel->totalRevenueOfScratchersGameInPeriod($ctx, $filters));

        $lotteryList = $revenueReportsModel->AllProductLottoInPeriod($ctx, $filters);
        $scratchersList = $revenueReportsModel->AllScratchersGameInPeriod($ctx, $filters);

        $scratchersList[0]['name'] = "Total Scratchers Sales";
        $lotteryList[0]['name']    = "Total Lottery Sales";
        $scratchersList[0]['total_amount']    = formatPrice($scratchersList[0]['total_amount']);
        $lotteryList[0]['total_amount']    = formatPrice($lotteryList[0]['total_amount']);

        $lotteryList = ($lotteryList[0]['total_amount'] === "0.00" ) ? array() : $lotteryList;
        $scratchersList = ($scratchersList[0]['total_amount'] === "0.00") ? array() : $scratchersList;

        $dommyList[0]['name'] = "Total "."Bear "."Sales";
        $dommyList[0]['total_amount'] = "0.00";
        $dommyList[1]['name'] = "Total "."Cigarate "."Sales";
        $dommyList[1]['total_amount'] = "0.00";
       
    } else {
        $summary                = $revenueReportsModel->totalSellingPriceAndTax($ctx, $filters);
        $ordersCompleted        = (int) $transactionReportsModel->ordersCount($ctx, $filters);
        $totalSellingPrice      = formatPrice($summary->sellingPrice);
        $totalTax               = formatPrice($summary->tax);
        $totalRevenue           = formatPrice($revenueReportsModel->totalRevenue($ctx, $filters));
        $totalRevenueCard       = formatPrice($revenueReportsModel->totalRevenueByPaymentMode($ctx, $filters));
        $totalRevenueCash       = formatPrice($revenueReportsModel->totalRevenueByPaymentMode($ctx, $cashModeFilters()));
        $totalRevenuePaytm      = formatPrice($revenueReportsModel->totalRevenueByPaymentMode($ctx, $paytmModeFilters()));
        // $totalLotteryRevenue    = formatPrice($revenueReportsModel->totalRevenueByProductLotto($ctx, $filters));
        // $totalScratchersRevenue = formatPrice($revenueReportsModel->totalRevenueOfScratchersGame($ctx, $filters));
    }

    $salesList = array_merge($lotteryList,$scratchersList);

    sendJSON(
        200,
        [
            "order_summary" => [
                "orders_completed"          => $ordersCompleted,
                "total_revenue"             => $totalRevenue,
                "total_revenue_cash"        => $totalRevenueCash,
                "total_revenue_card"        => $totalRevenueCard,
                "total_tax"                 => $totalTax,
                "total_special_fee"         => $ordersSpecialFee,
                "total_selling_price"       => $totalSellingPrice,
                "other_sales"               => array_merge($salesList,$dommyList)
            ]
        ],
        JSON_PRESERVE_ZERO_FRACTION
    );
}

/**
 * Product sales handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function productSalesV1($ctx, $args)
{
    $payload = payload();
    $input = [
        "fromDate" => arrVal($payload, "from_date"),                        // Formatted in m/d/Y
        "toDate"   => arrVal($payload, "to_date"),                          // Formatted in H:i a
        "fromTime" => arrVal($payload, "from_time"),                        // Formatted in m/d/Y
        "toTime"   => arrVal($payload, "to_time"),                          // Formatted in H:i a
        "tzID"     => arrVal($payload, "tzID", SERVER_TIMEZONE_ID),         // Timezone ID. E.g. Asia/Calcutta
        "tzShort"  => arrVal($payload, "tzShort", SERVER_TIMEZONE_SHORT),   // Timezone short name. E.g. IST, GMT, PST
    ];

    $v = new Validator;
    $v->name("tzID")->str($input["tzID"])->validTZ();
    $v->name("From date")->str($input["fromDate"])->reqStr()->formatDT(DEFAULT_DATE_INPUT_FMT)->compareDT($input["fromDate"],$input["toDate"]);
    $v->name("To date")->str($input["toDate"])->reqStr()->formatDT(DEFAULT_DATE_INPUT_FMT);

    $datesSet = function (&$input) {
        return (!empty($input["fromDate"]) && !empty($input["toDate"]));
    };
    $timesSet = function (&$input) {
        return (!empty($input["fromTime"]) && !empty($input["toTime"]));
    };

    if ($datesSet($input)) {
        $v->name("From date")->str($input["fromDate"])->reqStr()->formatDT(DEFAULT_DATE_INPUT_FMT);
        $v->name("To date")->str($input["toDate"])->reqStr()->formatDT(DEFAULT_DATE_INPUT_FMT);
        $input["fromDate"] = convertDateTimeFormat($input["fromDate"], "m/d/Y", "Y-m-d");
        $input["toDate"] = convertDateTimeFormat($input["toDate"], "m/d/Y", "Y-m-d");
    }

    if ($timesSet($input)) {
        $v->name("From time")->str($input["fromTime"])->reqStr()->formatDT(DEFAULT_TIME_INPUT_FMT);
        $v->name("To time")->str($input["toTime"])->reqStr()->formatDT(DEFAULT_TIME_INPUT_FMT);
        $input["fromTime"] = convertDateTimeFormat($input["fromTime"], "h:i A", "H:i:s");
        $input["toTime"] = convertDateTimeFormat($input["toTime"], "h:i A", "H:i:s");
    } else {
        $input["fromTime"] = "00:00:00";
        $input["toTime"] = "23:59:59";
    }

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $from = convertTimezone(
        "{$input["fromDate"]} {$input["fromTime"]}",
        $input["tzID"],
        SERVER_TIMEZONE_ID
    );
    $to = convertTimezone(
        "{$input["toDate"]} {$input["toTime"]}",
        $input["tzID"],
        SERVER_TIMEZONE_ID
    );

    Logger::infoMsg("Transactions summary from {$from} to {$to}");

    $filters = new stdClass;
    $filters->storeAdminID = $ctx->tokenData->store_admin_id;
    $filters->fromDate = $from;
    $filters->toDate = $to;
    $filters->orderStatus = ORDER_COMPLETED;

    $reportsModel = new SalesReports;

    if ($datesSet($input)) {
        $productSales = $reportsModel->productSalesInPeriod($ctx, $filters);
    } else {
        $productSales = $reportsModel->productSales($ctx, $filters);
    }

    if ($productSales) {
        sendJSON(200, ["product_sales" => $productSales], JSON_PRESERVE_ZERO_FRACTION);
    }
    sendErrJSON(404, ERR_SALES_PRODUCT_NOT_FOUND);
}

/**
 * List transactions handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function listTransactionsV1($ctx, $args)
{
    $payload = payload();
    $input = [
        "fromDate" => arrVal($payload, "from_date"),                        // Formatted in m/d/Y
        "toDate"   => arrVal($payload, "to_date"),                          // Formatted in H:i a
        "fromTime" => arrVal($payload, "from_time"),                        // Formatted in m/d/Y
        "toTime"   => arrVal($payload, "to_time"),                          // Formatted in H:i a
        "tzID"     => arrVal($payload, "tzID", SERVER_TIMEZONE_ID),         // Timezone ID. E.g. Asia/Calcutta
        "tzShort"  => arrVal($payload, "tzShort", SERVER_TIMEZONE_SHORT),   // Timezone short name. E.g. IST, GMT, PST
    ];

    $v = new Validator;
    $v->name("tzID")->str($input["tzID"])->validTZ();
    $v->name("From date")->str($input["fromDate"])->reqStr()->formatDT(DEFAULT_DATE_INPUT_FMT)->compareDT($input["fromDate"],$input["toDate"]);
    $v->name("To date")->str($input["toDate"])->reqStr()->formatDT(DEFAULT_DATE_INPUT_FMT);
    // $v->name("From time")->str($input["fromTime"])->reqStr()->formatDT(DEFAULT_TIME_INPUT_FMT)->compareTime($input["fromTime"],$input["toTime"]);
    // $v->name("To time")->str($input["toTime"])->reqStr()->formatDT(DEFAULT_TIME_INPUT_FMT);

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $input["fromDate"] = convertDateTimeFormat($input["fromDate"], "m/d/Y", "Y-m-d");
    $input["toDate"] = convertDateTimeFormat($input["toDate"], "m/d/Y", "Y-m-d");
    $input["fromTime"] = convertDateTimeFormat($input["fromTime"], "h:i A", "H:i:s");
    $input["toTime"] = convertDateTimeFormat($input["toTime"], "h:i A", "H:i:s");

    $from = convertTimezone(
        "{$input["fromDate"]} {$input["fromTime"]}",
        $input["tzID"],
        SERVER_TIMEZONE_ID
    );
    $to = convertTimezone(
        "{$input["toDate"]} {$input["toTime"]}",
        $input["tzID"],
        SERVER_TIMEZONE_ID
    );

    Logger::infoMsg("Transactions list from {$from} to {$to}");

    $filters = new stdClass;
    $filters->storeAdminID = $ctx->tokenData->store_admin_id;
    $filters->fromDate = $from;
    $filters->toDate = $to;
    $filters->orderStatus = ORDER_COMPLETED;
    $filters->paymentMode = ORDER_PAYMENT_MODE_CARD;

    // Fetch admin and cashiers info.
    $adminModel = new Admin;
    $admin = $adminModel->getInfoByID($ctx, $ctx->tokenData->store_admin_id);
    $cashiers = $adminModel->getCashiers(
        $ctx,
        $ctx->tokenData->store_admin_id,
        $ctx->tokenData->registered_app
    );

    // Build a map of id->name for admin and all cashiers.
    // ALERT: BAD DESIGN: What if admin and cashier has same id from different tables?
    $people[$admin["id"]] = $admin["name"];

    foreach ($cashiers as $cashier) {
        $people[$cashier["id"]] = "{$cashier["first_name"]} {$cashier["last_name"]}";
    }

    $reportsModel = new TransactionReports;
    $orders = $reportsModel->ordersInPeriod($ctx, $filters);
    Logger::infoMsg(sprintf("Returned transactions count: %d", count($orders)));

    // Update order fields.
    foreach ($orders as &$order) {
        $order["amount"] = (float) $order["amount"];
        $order["tms"] = convertTimezone($order["tms"], SERVER_TIMEZONE_ID, $input["tzID"]);
        $order["payment_mode"] = !empty($order["payment_mode"]) ? ucwords($order["payment_mode"]) : NULL;
        $order["user_id"] = (int) $order["user_id"];
        $order["clerk"] = isset($people[(int) $order["user_id"]]) ? $people[(int) $order["user_id"]] : NULL;
    }
    if ($orders) {
        sendJSON(200, ["transaction_history" => $orders]);
    }
    sendErrJSON(404, ERR_TRANSACTION_NOT_FOUND);
}

/**
 * Transactions summary handler for email & pdf.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function requestOrderSummaryReportPdf($ctx, $args)
{
    $payload = payload();
    $input = [
        "fromDate" => arrVal($payload, "from_date"),                        // Formatted in m/d/Y
        "toDate"   => arrVal($payload, "to_date"),                          // Formatted in H:i a
        "fromTime" => arrVal($payload, "from_time"),                        // Formatted in m/d/Y
        "toTime"   => arrVal($payload, "to_time"),                          // Formatted in H:i a
        "tzID"     => arrVal($payload, "tzID", SERVER_TIMEZONE_ID),         // Timezone ID. E.g. Asia/Calcutta
        "tzShort"  => arrVal($payload, "tzShort", SERVER_TIMEZONE_SHORT),   // Timezone short name. E.g. IST, GMT, PST
    ];

    $v = new Validator;
    $v->name("tzID")->str($input["tzID"])->validTZ();
    $v->name("From date")->str($input["fromDate"])->reqStr()->formatDT(DEFAULT_DATE_INPUT_FMT)->compareDT($input["fromDate"],$input["toDate"]);
    $v->name("To date")->str($input["toDate"])->reqStr()->formatDT(DEFAULT_DATE_INPUT_FMT);

    $datesSet = function (&$input) {
        return (!empty($input["fromDate"]) && !empty($input["toDate"]));
    };
    $timesSet = function (&$input) {
        return (!empty($input["fromTime"]) && !empty($input["toTime"]));
    };

    if ($datesSet($input)) {
        $v->name("From date")->str($input["fromDate"])->reqStr()->formatDT(DEFAULT_DATE_INPUT_FMT);
        $v->name("To date")->str($input["toDate"])->reqStr()->formatDT(DEFAULT_DATE_INPUT_FMT);
        $input["fromDate"] = convertDateTimeFormat($input["fromDate"], "m/d/Y", "Y-m-d");
        $input["toDate"] = convertDateTimeFormat($input["toDate"], "m/d/Y", "Y-m-d");
    }

    if ($timesSet($input)) {
        $v->name("From time")->str($input["fromTime"])->reqStr()->formatDT(DEFAULT_TIME_INPUT_FMT);
        $v->name("To time")->str($input["toTime"])->reqStr()->formatDT(DEFAULT_TIME_INPUT_FMT);
        $input["fromTime"] = convertDateTimeFormat($input["fromTime"], "h:i A", "H:i:s");
        $input["toTime"] = convertDateTimeFormat($input["toTime"], "h:i A", "H:i:s");
    } else {
        $input["fromTime"] = "00:00:00";
        $input["toTime"] = "23:59:59";
    }

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $from = convertTimezone(
        "{$input["fromDate"]} {$input["fromTime"]}",
        $input["tzID"],
        SERVER_TIMEZONE_ID
    );
    $to = convertTimezone(
        "{$input["toDate"]} {$input["toTime"]}",
        $input["tzID"],
        SERVER_TIMEZONE_ID
    );

    Logger::infoMsg("Transactions summary from {$from} to {$to}");

    $filters = new stdClass;
    $filters->storeAdminID = $ctx->tokenData->store_admin_id;
    $filters->fromDate = $from;
    $filters->toDate = $to;
    $filters->orderStatus = ORDER_COMPLETED;
    $filters->paymentMode = ORDER_PAYMENT_MODE_CARD;
    $cashModeFilters = function () use ($filters) {
        $newFilters = clone $filters;
        $newFilters->paymentMode = ORDER_PAYMENT_MODE_CASH;
        return $newFilters;
    };
    $paytmModeFilters = function () use ($filters) {
        $newFilters = clone $filters;
        $newFilters->paymentMode = ORDER_PAYMENT_MODE_PAYTM;
        return $newFilters;
    };

    $revenueReportsModel = new RevenueReports;
    $transactionReportsModel = new TransactionReports;

    if ($datesSet($input)) {
        $summary                = $revenueReportsModel->totalSellingPriceAndTaxInPeriod($ctx, $filters);
        $ordersCompleted        = (int) $transactionReportsModel->ordersCountInPeriod($ctx, $filters);
        $totalSellingPrice      = formatPrice($summary->sellingPrice);
        $totalTax               = formatPrice($summary->tax);
        $totalRevenue           = formatPrice($revenueReportsModel->totalRevenueInPeriod($ctx, $filters));
        $totalRevenueCard       = formatPrice($revenueReportsModel->totalRevenueByPaymentModeInPeriod($ctx, $filters));
        $totalRevenueCash       = formatPrice($revenueReportsModel->totalRevenueByPaymentModeInPeriod($ctx, $cashModeFilters()));
        $totalRevenuePaytm      = formatPrice($revenueReportsModel->totalRevenueByPaymentModeInPeriod($ctx, $paytmModeFilters()));
        $totalLotteryRevenue    = formatPrice($revenueReportsModel->totalRevenueByProductLottoInPeriod($ctx, $filters));
        $totalScratchersRevenue = formatPrice($revenueReportsModel->totalRevenueOfScratchersGameInPeriod($ctx, $filters));
    } else {
        $summary                = $revenueReportsModel->totalSellingPriceAndTax($ctx, $filters);
        $ordersCompleted        = (int) $transactionReportsModel->ordersCount($ctx, $filters);
        $totalSellingPrice      = formatPrice($summary->sellingPrice);
        $totalTax               = formatPrice($summary->tax);
        $totalRevenue           = formatPrice($revenueReportsModel->totalRevenue($ctx, $filters));
        $totalRevenueCard       = formatPrice($revenueReportsModel->totalRevenueByPaymentMode($ctx, $filters));
        $totalRevenueCash       = formatPrice($revenueReportsModel->totalRevenueByPaymentMode($ctx, $cashModeFilters()));
        $totalRevenuePaytm      = formatPrice($revenueReportsModel->totalRevenueByPaymentMode($ctx, $paytmModeFilters()));
        $totalLotteryRevenue    = formatPrice($revenueReportsModel->totalRevenueByProductLotto($ctx, $filters));
        $totalScratchersRevenue = formatPrice($revenueReportsModel->totalRevenueOfScratchersGame($ctx, $filters));
    }
    // Fetch admin info.
    $admin = (new Admin)->getInfoByID($ctx, $ctx->tokenData->store_admin_id);

    //make TCPDF object
    $pdf= new TCPDF('P','MM','A4');
    //add page
    $pdf->AddPage();

    $pdf->setFont('Helvetica','',16);
    $pdf->Cell(190,10,'Orders Summary Report.',0,1,'C');
    $pdf->setFont('Helvetica','',14);
    $pdf->Cell(190,10,'Plat4m Inc.',0,1,'L');
    $pdf->setFont('Helvetica','',10);
    $pdf->Cell(30,5,$admin['store_name'],0);
    $pdf->Cell(160,5,'From Date : ' . $input["fromDate"],0,0,'R');
    $pdf->Ln();
    $pdf->Cell(30,5,$admin['mobile_number'],0);
    $pdf->Cell(160,5,'To Date : ' . $input["toDate"],0,0,'R');
    $pdf->Ln();
    $pdf->setFont('Helvetica','',10);
    $pdf->Cell(30,5,$admin['street_address'],0,1,'L');
    $pdf->Cell(30,5,$admin['store_city'] . ',' . $admin['store_zip'] . ',' . $admin['store_country'],0,1,'L');
    $pdf->Ln();
    $pdf->Ln(2);

   $tbl = <<<EOD
            <table border="1" cellpadding="2" cellspacing="2" align="center">
             <tr nobr="true">
              <th colspan="2">Order Summary</th>
             </tr>
             <tr nobr="true">
              <td>Orders Completed</td>
        EOD;

    $tbl .= '<td>' .$ordersCompleted.'</td>
             </tr>
             <tr nobr="true">
              <td>Total Selling Price</td>
              <td>'.$totalSellingPrice.'</td>
             </tr>
             <tr nobr="true">
              <td>Total Tax</td>
              <td>'.$totalTax.'</td>
             </tr>
             <tr nobr="true">
              <td>Total Revenue</td>
              <td>'.$totalRevenue.'</td>
             </tr>
             <tr nobr="true">
              <td>Total Revenue Cash</td>
              <td>'.$totalRevenueCash.'</td>
             </tr>
             <tr nobr="true">
              <td>Total Revenue Card</td>
              <td>'.$totalRevenueCard.'</td>
             </tr>
             <tr nobr="true">
              <td>Total Revenue Paytm</td>
              <td>'.$totalRevenuePaytm.'</td>
             </tr>
             <tr nobr="true">
              <td>Total Lottery Revenue</td>
              <td>'.$totalLotteryRevenue.'</td>
             </tr>
             <tr nobr="true">
              <td>Total Scratchers Revenue</td>
              <td>'.$totalScratchersRevenue.'</td>
             </tr>';
    $tbl .='
            </table>
            <style>
                table{
                    border-collapse: collapse;
                }
                th,td{
                    border: 1px solid #888;
                }
                table tr th{
                    background-color: #888;
                    color: #fff;
                    font-weight: bold;
                }
            </style>';
    $pdf->writeHTML($tbl);
    $file_name = $input["fromDate"] . "-" . $input["toDate"] . '.pdf';
    $file = $pdf->Output("","S"); //save pdf file.

    //Send pdf report to mail.
    $email = $ctx->tokenData->email;
    $name = $ctx->tokenData->name;
    $subject ="Order Summary Report";
    $body = "Hi <b>{$name}</b><br><br>";
    $body .= "Please find your requested order summary reports from below attachment. <br><br>";
    $body .= "Thanks<br>";
    $body .= "Plat4m Inc.";

    $result = (new Mailer)->send($email, $name, $subject, $body,$file,$file_name);

    if ($result == 1) {
        sendMsgJSON(200, "Order Summary Report has sent to the registered email. Please check.");
    }
    sendErrJSON(500, JSON_PRESERVE_ZERO_FRACTION); 
}

/**
 * Create Product Sales Report pdf & send to mail.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function requestProductSalesReportPdf($ctx, $args)
{
    $payload = payload();
    $input = [
        "fromDate" => arrVal($payload, "from_date"),                        // Formatted in m/d/Y
        "toDate"   => arrVal($payload, "to_date"),                          // Formatted in H:i a
        "fromTime" => arrVal($payload, "from_time"),                        // Formatted in m/d/Y
        "toTime"   => arrVal($payload, "to_time"),                          // Formatted in H:i a
        "tzID"     => arrVal($payload, "tzID", SERVER_TIMEZONE_ID),         // Timezone ID. E.g. Asia/Calcutta
        "tzShort"  => arrVal($payload, "tzShort", SERVER_TIMEZONE_SHORT),   // Timezone short name. E.g. IST, GMT, PST
    ];

    $v = new Validator;
    $v->name("tzID")->str($input["tzID"])->validTZ();
    $v->name("From date")->str($input["fromDate"])->reqStr()->formatDT(DEFAULT_DATE_INPUT_FMT)->compareDT($input["fromDate"],$input["toDate"]);
    $v->name("To date")->str($input["toDate"])->reqStr()->formatDT(DEFAULT_DATE_INPUT_FMT);

    $datesSet = function (&$input) {
        return (!empty($input["fromDate"]) && !empty($input["toDate"]));
    };
    $timesSet = function (&$input) {
        return (!empty($input["fromTime"]) && !empty($input["toTime"]));
    };

    if ($datesSet($input)) {
        $v->name("From date")->str($input["fromDate"])->reqStr()->formatDT(DEFAULT_DATE_INPUT_FMT);
        $v->name("To date")->str($input["toDate"])->reqStr()->formatDT(DEFAULT_DATE_INPUT_FMT);
        $input["fromDate"] = convertDateTimeFormat($input["fromDate"], "m/d/Y", "Y-m-d");
        $input["toDate"] = convertDateTimeFormat($input["toDate"], "m/d/Y", "Y-m-d");
    }

    if ($timesSet($input)) {
        $v->name("From time")->str($input["fromTime"])->reqStr()->formatDT(DEFAULT_TIME_INPUT_FMT);
        $v->name("To time")->str($input["toTime"])->reqStr()->formatDT(DEFAULT_TIME_INPUT_FMT);
        $input["fromTime"] = convertDateTimeFormat($input["fromTime"], "h:i A", "H:i:s");
        $input["toTime"] = convertDateTimeFormat($input["toTime"], "h:i A", "H:i:s");
    } else {
        $input["fromTime"] = "00:00:00";
        $input["toTime"] = "23:59:59";
    }

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $from = convertTimezone(
        "{$input["fromDate"]} {$input["fromTime"]}",
        $input["tzID"],
        SERVER_TIMEZONE_ID
    );
    $to = convertTimezone(
        "{$input["toDate"]} {$input["toTime"]}",
        $input["tzID"],
        SERVER_TIMEZONE_ID
    );

    Logger::infoMsg("Transactions summary from {$from} to {$to}");

    $filters = new stdClass;
    $filters->storeAdminID = $ctx->tokenData->store_admin_id;
    $filters->fromDate = $from;
    $filters->toDate = $to;
    $filters->orderStatus = ORDER_COMPLETED;

    $reportsModel = new SalesReports;

    if ($datesSet($input)) {
        $productSales = $reportsModel->productSalesInPeriod($ctx, $filters);
    } else {
        $productSales = $reportsModel->productSales($ctx, $filters);
    }

    if (!$productSales) {
         sendErrJSON(400, ERR_SALES_PRODUCT_NOT_FOUND);
    }

    // Fetch admin info.
    $admin = (new Admin)->getInfoByID($ctx, $ctx->tokenData->store_admin_id);
    //make TCPDF object
    $pdf= new TCPDF('P','MM','A4');
    //add page
    $pdf->AddPage();

    $pdf->setFont('Helvetica','',16);
    $pdf->Cell(190,10,'Product Sales Report.',0,1,'C');
    $pdf->setFont('Helvetica','',14);
    $pdf->Cell(190,10,'Plat4m Inc.',0,1,'L');
    $pdf->setFont('Helvetica','',10);
    $pdf->Cell(30,5,$admin['store_name'],0);
    $pdf->Cell(160,5,'From Date : ' . $input["fromDate"],0,0,'R');
    $pdf->Ln();
    $pdf->Cell(30,5,$admin['mobile_number'],0);
    $pdf->Cell(160,5,'To Date : ' . $input["toDate"],0,0,'R');
    $pdf->Ln();
    $pdf->setFont('Helvetica','',10);
    $pdf->Cell(30,5,$admin['street_address'],0,1,'L');
    $pdf->Cell(30,5,$admin['store_city'] . ',' . $admin['store_zip'] . ',' . $admin['store_country'],0,1,'L');
    $pdf->Ln();
    $pdf->Ln(2);

    $tbl_header = '<table>';
    $html = '';
    //add Content
    $html .= '
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Selling Price</th>
            </tr>
        </thead>
        <tbody>' ;
        foreach($productSales as $k => $v) {
    $html .= '
        <tr>
            <td>'.$v['product_name'].'</td>
            <td >'.$v['quantity'].'</td>
            <td>'.$v['selling_price'].'</td>
        </tr>';
        }
    $html .= '</tbody>';

    $html .="
            </table>
            <style>
                table{
                    border-collapse: collapse;
                }
                th,td{
                    border: 1px solid #888;
                }
                table tr th{
                    background-color: #888;
                    color: #fff;
                    font-weight: bold;
                }
            </style>
    ";

    $pdf->writeHTML($tbl_header . $html);
    $file_name = $input["fromDate"] . "-" . $input["toDate"] . '.pdf';
    $file = $pdf->Output("","S"); //save pdf file.

    //Send pdf report to mail.
    $email = $ctx->tokenData->email;
    $name = $ctx->tokenData->name;
    $subject ="Product sales Report";
    $body = "Hi <b>{$name}</b><br><br>";
    $body .= "Please find your requested product sales reports from below attachment. <br><br>";
    $body .= "Thanks<br>";
    $body .= "Plat4m Inc.";

    $result = (new Mailer)->send($email, $name, $subject, $body,$file,$file_name);

    if ($result == 1) {
        sendMsgJSON(200, "Product Sales Report has sent to the registered email. Please check.");
    }

    sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR, "No Sales Product Found");
}

/**
 * List transactions Pdf report for send email.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function requestTransactionReportPdf($ctx, $args)
{
    $payload = payload();
    $input = [
        "fromDate" => arrVal($payload, "from_date"),                        // Formatted in m/d/Y
        "toDate"   => arrVal($payload, "to_date"),                          // Formatted in H:i a
        "fromTime" => arrVal($payload, "from_time"),                        // Formatted in m/d/Y
        "toTime"   => arrVal($payload, "to_time"),                          // Formatted in H:i a
        "tzID"     => arrVal($payload, "tzID", SERVER_TIMEZONE_ID),         // Timezone ID. E.g. Asia/Calcutta
        "tzShort"  => arrVal($payload, "tzShort", SERVER_TIMEZONE_SHORT),   // Timezone short name. E.g. IST, GMT, PST
    ];

    $v = new Validator;
    $v->name("tzID")->str($input["tzID"])->validTZ();
    $v->name("From date")->str($input["fromDate"])->reqStr()->formatDT(DEFAULT_DATE_INPUT_FMT)->compareDT($input["fromDate"],$input["toDate"]);
    $v->name("To date")->str($input["toDate"])->reqStr()->formatDT(DEFAULT_DATE_INPUT_FMT);

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $input["fromDate"] = convertDateTimeFormat($input["fromDate"], "m/d/Y", "Y-m-d");
    $input["toDate"] = convertDateTimeFormat($input["toDate"], "m/d/Y", "Y-m-d");
    $input["fromTime"] = convertDateTimeFormat($input["fromTime"], "h:i A", "H:i:s");
    $input["toTime"] = convertDateTimeFormat($input["toTime"], "h:i A", "H:i:s");

    $from = convertTimezone(
        "{$input["fromDate"]} {$input["fromTime"]}",
        $input["tzID"],
        SERVER_TIMEZONE_ID
    );
    $to = convertTimezone(
        "{$input["toDate"]} {$input["toTime"]}",
        $input["tzID"],
        SERVER_TIMEZONE_ID
    );

    Logger::infoMsg("Transactions list from {$from} to {$to}");

    $filters = new stdClass;
    $filters->storeAdminID = $ctx->tokenData->store_admin_id;
    $filters->fromDate = $from;
    $filters->toDate = $to;
    $filters->orderStatus = ORDER_COMPLETED;
    $filters->paymentMode = ORDER_PAYMENT_MODE_CARD;

    // Fetch admin and cashiers info.
    $adminModel = new Admin;
    $admin = $adminModel->getInfoByID($ctx, $ctx->tokenData->store_admin_id);
    $cashiers = $adminModel->getCashiers(
        $ctx,
        $ctx->tokenData->store_admin_id,
        $ctx->tokenData->registered_app
    );

    // Build a map of id->name for admin and all cashiers.
    // ALERT: BAD DESIGN: What if admin and cashier has same id from different tables?
    $people[$admin["id"]] = $admin["name"];

    foreach ($cashiers as $cashier) {
        $people[$cashier["id"]] = "{$cashier["first_name"]} {$cashier["last_name"]}";
    }

    $reportsModel = new TransactionReports;
    $orders = $reportsModel->ordersInPeriod($ctx, $filters);
    Logger::infoMsg(sprintf("Returned transactions count: %d", count($orders)));

    // Update order fields.
    foreach ($orders as &$order) {
        $order["amount"] = (float) $order["amount"];
        $order["tms"] = convertTimezone($order["tms"], SERVER_TIMEZONE_ID, $input["tzID"]);
        $order["payment_mode"] = !empty($order["payment_mode"]) ? ucwords($order["payment_mode"]) : NULL;
        $order["user_id"] = (int) $order["user_id"];
        $order["clerk"] = isset($people[(int) $order["user_id"]]) ? $people[(int) $order["user_id"]] : NULL;
    }

    if ($orders) {
         //make TCPDF object
        $pdf= new TCPDF('P','MM','A4');
        //add page
        $pdf->AddPage();

        $pdf->setFont('Helvetica','',16);
        $pdf->Cell(190,10,'Transactions Report.',0,1,'C');
        $pdf->setFont('Helvetica','',14);
        $pdf->Cell(190,10,'Plat4m Inc.',0,1,'L');
        $pdf->setFont('Helvetica','',10);
        $pdf->Cell(30,5,$admin['store_name'],0);
        $pdf->Cell(160,5,'From Date : ' . $input["fromDate"],0,0,'R');
        $pdf->Ln();
        $pdf->Cell(30,5,$admin['mobile_number'],0);
        $pdf->Cell(160,5,'To Date : ' . $input["toDate"],0,0,'R');
        $pdf->Ln();
        $pdf->setFont('Helvetica','',10);
        $pdf->Cell(30,5,$admin['street_address'],0,1,'L');
        $pdf->Cell(30,5,$admin['store_city'] . ',' . $admin['store_zip'] . ',' . $admin['store_country'],0,1,'L');
        $pdf->Ln();
        $pdf->Ln(2);

        $tbl_header = '<table>';
        $html = '';
        $i=0;
        //add Content
        $html .= '
            <thead>
                <tr>
                    <th style="width:37px">Sl.No</th>
                    <th>Name / Clerk</th>
                    <th>Order Unique Id</th>
                    <th>Amount</th>
                    <th>Payment mode</th>
                    <th>Times</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>' ;
            foreach($orders as $k => $v) {
        $html .= '
            <tr>
                <td style="width:37px">'.++$i.'</td>
                <td>'.$v['clerk'].'</td>
                <td>'.$v['order_uuid'].'</td>
                <td>'.$v['amount'].'</td>
                <td>'.$v['payment_mode'].'</td>
                <td>'.$v['tms'].'</td>
                <td>'.$v['status'].'</td>
            </tr>';
            }
        $html .= '</tbody>';

        $html .="
                </table>
                <style>
                    table{
                        border-collapse: collapse;
                    }
                    th,td{
                        border: 1px solid #888;
                    }
                    table tr th{
                        background-color: #888;
                        color: #fff;
                        font-weight: bold;
                    }
                </style>
        ";

        $pdf->writeHTML($tbl_header . $html);
        $file_name = $input["fromDate"] . "-" . $input["toDate"] . '.pdf';
        $file = $pdf->Output("","S"); //save pdf file.

        //Send pdf report to mail.
        $email = $ctx->tokenData->email;
        $name = $ctx->tokenData->name;
        $subject ="Product sales Report";
        $body = "Hi <b>{$name}</b><br><br>";
        $body .= "Please find your requested product sales reports from below attachment. <br><br>";
        $body .= "Thanks<br>";
        $body .= "Plat4m Inc.";

        $result = (new Mailer)->send($email, $name, $subject, $body,$file,$file_name);

        if ($result == 1) {
            sendMsgJSON(200, "Transactions Report has sent to the registered email. Please check.");
        }
    }
    sendErrJSON(404, ERR_TRANSACTION_NOT_FOUND);
}

/**
 * Send a receipt to mail.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function receiptEmail($ctx, $args)
{
    $payload = payload();
    $input = [
        "email"      => arrVal($payload, "email"),               
        "order_uuid"   => arrVal($payload, "order_uuid")
    ];

    $v = new Validator;
    $v->name("Email")->str($input["email"])->reqStr()->strEmail();
    $v->name("Order UUID")->str($input["order_uuid"])->reqStr();

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $transactionModel = new Transaction;

    $orderUUIDExists= $transactionModel->orderIDExists($ctx,$input["order_uuid"]);
    if (!$orderUUIDExists) {
        sendJSON(200, ["message" => "Invalid Order UUId"]);
    }

    $orderProducts = $transactionModel->getOrderProductsByOrderRowID(
        $ctx,
        $orderUUIDExists
    );

    foreach ($orderProducts as &$product) {
        $productExistsInStore= $transactionModel->productExists($ctx, $product["product_id"]);
        $productExistsInTempStore= $transactionModel->tempProductExists($ctx, $product["product_id"]);

        if ($productExistsInStore) {
            // $soldout_quantity = $transactionModel->getSoldOutQty($ctx, $product["product_id"]);
            $product["stock_quantity"] = $productExistsInStore[0]['Stock_Quantity'];
        } else if ($productExistsInTempStore) {
            // $soldout_quantity = $transactionModel->getSoldOutQty($ctx, $product["product_id"]);
            $product["stock_quantity"] = $productExistsInTempStore[0]['stock_quantity'];
        } else {
            $product["stock_quantity"] = 0;
        }

    }

   /* $orderInfo["products"] = $orderProducts;
    sendJSON(200, $orderInfo);*/

    
    // Fetch admin info.
    $admin = (new Admin)->getInfoByID($ctx, $ctx->tokenData->store_admin_id);
    //make TCPDF object
    $pdf= new TCPDF('P','MM','A4');
    //add page
    $pdf->AddPage();

    $pdf->setFont('Helvetica','',16);
    $pdf->Cell(190,10,'Product Receipt',0,1,'C');
    $pdf->setFont('Helvetica','',14);
    $pdf->Cell(190,10,'Plat4m Inc.',0,1,'L');
    $pdf->setFont('Helvetica','',10);
    $pdf->Cell(30,5,$admin['store_name'],0);
    $pdf->Ln();
    $pdf->Cell(30,5,$admin['mobile_number'],0);
    $pdf->Ln();
    $pdf->setFont('Helvetica','',10);
    $pdf->Cell(30,5,$admin['street_address'],0,1,'L');
    $pdf->Cell(30,5,$admin['store_city'] . ',' . $admin['store_zip'] . ',' . $admin['store_country'],0,1,'L');
    $pdf->Ln();
    $pdf->Ln(2);

    $tbl_header = '<table>';
    $html = '';
    //add Content
    $html .= '
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>' ;
        foreach($orderProducts as $k => $v) {
    $html .= '
        <tr>
            <td>'.$v['name'].'</td>
            <td >'.$v['quantity'].'</td>
            <td>'.$v['selling_price'].'</td>
        </tr>';
        }
    $html .= '</tbody>';

    $html .="
            </table>
            <style>
                table{
                    border-collapse: collapse;
                }
                th,td{
                    border: 1px solid #888;
                }
                table tr th{
                    background-color: #888;
                    color: #fff;
                    font-weight: bold;
                }
            </style>
    ";

    $pdf->writeHTML($tbl_header . $html);
    $file_name = $input["order_uuid"] . '.pdf';
    $file = $pdf->Output("","S"); //save pdf file.

    //Send pdf report to mail.
    $email = $input["email"];
    $name = $ctx->tokenData->name;
    $subject ="Product sales Report";
    $body = "Hi <b>{$name}</b><br><br>";
    $body .= "Please find receipt from below attachment. <br><br>";
    $body .= "Thanks<br>";
    $body .= "Plat4m Inc.";

    $result = (new Mailer)->send($email, $name, $subject, $body,$file,$file_name);

    if ($result == 1) {
        sendMsgJSON(200, "Product receipt has sent to email. Please check.");
    }

    sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR, "No Product receipt Found");
}

/**
 * Send a sms to user mobile number.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function sendSms($ctx, $args)
{
    $payload = payload();
    $input = [
        "mobile_number"=> arrVal($payload, "mobile_number"),
        "order_uuid"   => arrVal($payload, "order_uuid")           
    ];

    $v = new Validator;
    $v->name("Mobile number")->str($input["mobile_number"])->reqStr();
    $v->name("Order UUID")->str($input["order_uuid"])->reqStr();

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $transactionModel = new Transaction;

    $orderUUIDExists= $transactionModel->orderIDExists($ctx,$input["order_uuid"]);
    if (!$orderUUIDExists) {
        sendJSON(200, ["message" => "Invalid Order UUId"]);
    }

    $orderProducts = $transactionModel->getOrderProductsByOrderRowID(
        $ctx,
        $orderUUIDExists
    );

    foreach ($orderProducts as &$product) {
        $productExistsInStore= $transactionModel->productExists($ctx, $product["product_id"]);
        $productExistsInTempStore= $transactionModel->tempProductExists($ctx, $product["product_id"]);

        if ($productExistsInStore) {
            // $soldout_quantity = $transactionModel->getSoldOutQty($ctx, $product["product_id"]);
            $product["stock_quantity"] = $productExistsInStore[0]['Stock_Quantity'];
        } else if ($productExistsInTempStore) {
            // $soldout_quantity = $transactionModel->getSoldOutQty($ctx, $product["product_id"]);
            $product["stock_quantity"] = $productExistsInTempStore[0]['stock_quantity'];
        } else {
            $product["stock_quantity"] = 0;
        }

    }
    
    // Fetch admin info.
    $admin = (new Admin)->getInfoByID($ctx, $ctx->tokenData->store_admin_id);
    
    // Authorisation details.
    $username = "harihara.sahoo@assettl.in";
    $hash = "1ac6e6aeb54eb8277f848ccb3c3354b19ecb2d17ab3729cac7d4240127fd1873";

    // Config variables. Consult http://api.textlocal.in/docs for more info.
    $test = "https://dev.plat4minc.com/";

    // Data for text message. This is the text message data.
    $sender = "600010"; // This is who the message appears to be from.
    $numbers = $input["mobile_number"]; // A single number or a comma-seperated list of numbers
    $message = "Hi there, thank you for sending your first test message from Textlocal. See how you can send effective SMS campaigns here: https://tx.gl/r/2nGVj/";
    // 612 chars or less
    // A single number or a comma-seperated list of numbers
    $message = urlencode($message);
    $data = "username=".$username."&hash=".$hash."&message=".$message."&sender=".$sender."&numbers=".$numbers."&test=".$test;
    $ch = curl_init('http://api.textlocal.in/send/?');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch); // This is the result from the API
    curl_close($ch);
    // Process your response here
    sendJSON(200, ["message" => $result]);

    // sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR, "No Receipt Found");
}

/**
 * Send a WhatsApp to user.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
/*function sendWhatsApp1($ctx, $args)
{
    $payload = payload();
    $input = [
        "mobile_number"=> arrVal($payload, "mobile_number"),
        "order_uuid"   => arrVal($payload, "order_uuid")           
    ];

    $v = new Validator;
    $v->name("Mobile number")->str($input["mobile_number"])->reqStr();
    $v->name("Order UUID")->str($input["order_uuid"])->reqStr();

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $transactionModel = new Transaction;

    $orderUUIDExists= $transactionModel->orderIDExists($ctx,$input["order_uuid"]);
    if (!$orderUUIDExists) {
        sendJSON(200, ["message" => "Invalid Order UUId"]);
    }

    $orderProducts = $transactionModel->getOrderProductsByOrderRowID(
        $ctx,
        $orderUUIDExists
    );


    foreach ($orderProducts as &$product) {
        $productExistsInStore= $transactionModel->productExists($ctx, $product["product_id"]);
        $productExistsInTempStore= $transactionModel->tempProductExists($ctx, $product["product_id"]);

        if ($productExistsInStore) {
            // $soldout_quantity = $transactionModel->getSoldOutQty($ctx, $product["product_id"]);
            $product["stock_quantity"] = $productExistsInStore[0]['Stock_Quantity'];
        } else if ($productExistsInTempStore) {
            // $soldout_quantity = $transactionModel->getSoldOutQty($ctx, $product["product_id"]);
            $product["stock_quantity"] = $productExistsInTempStore[0]['stock_quantity'];
        } else {
            $product["stock_quantity"] = 0;
        }

    }
    
    // Fetch admin info.
    $admin = (new Admin)->getInfoByID($ctx, $ctx->tokenData->store_admin_id);

    $data = "Thank you ".$ctx->tokenData->name;
    $data .= "Here’s a quick update on your Order with Order Id-: ".$input["order_uuid"];
    
    $obj = new WhatsappAPI('2534', '3cd8473d03f18163c21a27e9237277af427f1581'); // create object by passing your User ID and API Key
    $status = $obj->send($input["mobile_number"], $data); // NOTE: Phone Number should be with country code
    $status = json_decode($status);
    
    sendJSON(200, ["message" => $status]);

    sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR, "No Receipt Found");
}

function sendWhatsApp($ctx, $args)
{
    $payload = payload();
    $input = [
        "mobile_number"=> arrVal($payload, "mobile_number"),
        "order_uuid"   => arrVal($payload, "order_uuid")           
    ];

     $data = array('number' => '916372742539', 'enable' => 'true','message'=>'test%20message','instance_id'=>'625E2623EA569','access_token'=>'4b8f6660733f5d387724ac66d78334c1');

    $url = "https://betablaster.in/api/send.php?number=".$input['mobile_number']."&type=text&message=test%20message&instance_id=625F8C33979E7&access_token=97e872fd3825512788a63f885aa802ec";
    $ch = curl_init( $url );
    curl_setopt( $ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt( $ch, CURLOPT_HEADER, 0);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec( $ch );
    
    sendJSON(200, ["message" => $response]);

    sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR, "No Receipt Found");
}

function sendWhatsApp3($ctx, $args)
{
    $client = new Client([
    'base_uri' => "https://vjeqem.api.infobip.com/",
    'headers' => [
        'Authorization' => "App 3339929cd4436588e4e76cf02dd187a6-a2d01b03-52b8-4098-8531-efce197ccf4f",
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ]
]);

$response = $client->request(
    'POST',
    'whatsapp/1/message/template',
    [
        RequestOptions::JSON => [
            'messages' => [
                [
                    'from' => '917008331217',
                    'to' => "917328035453",
                    'content' => [
                        'templateName' => 'registration_success',
                        'templateData' => [
                            'body' => [
                                'placeholders' => ['sender', 'message', 'delivered', 'testing']
                            ],
                            'header' => [
                                'type' => 'IMAGE',
                                'mediaUrl' => 'https://api.infobip.com/ott/1/media/infobipLogo',
                            ],
                            'buttons' => [
                                ['type' => 'QUICK_REPLY', 'parameter' => 'yes-payload'],
                                ['type' => 'QUICK_REPLY', 'parameter' => 'no-payload'],
                                ['type' => 'QUICK_REPLY', 'parameter' => 'later-payload']
                            ]
                        ],
                        'language' => 'en',
                    ],
                ]
            ]
        ],
    ]
);

echo("HTTP code: " . $response->getStatusCode() . PHP_EOL);
echo("Response body: " . $response->getBody()->getContents() . PHP_EOL);
}*/
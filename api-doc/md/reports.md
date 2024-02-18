# Reports

Reports generated from transactions.

Input parameters.

Attribute   | Type   | Description
----------- | ------ | --------------------------
`from_date` | string | From date in `MM/DD/YYYY` format. E.g. 05/14/2021
`from_time` | string | From time in `HH:MM AM` format. E.g. 12:00 AM
`to_date`   | string | To date in `MM/DD/YYYY` format. E.g. 05/14/2021
`to_time`   | string | To time in `HH:MM AM` format. E.g. 12:00 AM
`tzID`      | string | Timezone ID. E.g. America/Los_Angeles

Output parameters for **orders summary**.

Attribute                | Type  | Description
------------------------ | ----- | --------------------------
`orders_completed`       | int   | Number of orders completed.
`total_selling_price`    | float | Total selling price.
`total_tax`              | float | Total tax.
`total_revenue`          | float | Total revenue.
`total_revenue_cash`     | float | Total revenue from orders paid via cash.
`total_revenue_card`     | float | Total revenue from orders paid via card.
`total_lottery_sales`    | float | Total sales of Lottery.
`total_scratchers_sales` | float | Total sales of Scratchers.

Output parameters for **product sales**.

Attribute       | Type   | Description
--------------- | ------ | --------------------------
`product_name`  | string | Product name.
`quantity`      | int    | Total number of products sold.
`selling_price` | float  | Total selling price.

Output parameters for **transactions**.

Attribute      | Type   | Description
---------------| ------ | --------------------------
`id`           | int    | Order ID.
`order_uuid`   | string | Order UUID.
`amount`       | float  | Amount.
`status`       | string | Order status. E.g. Complete
`tms`          | string | Timestamp.
`user_id`      | int    | User (admin or cashier) ID.
`payment_mode` | string | Payment mode. Cash or card.
`clerk`        | string | Clerk (admin or cashier) name.

---

## *Orders summary*

### **POST** `/api/v3/reports/orders-summary`

Summary of orders between a time period.

#### Request

    {
        "from_date": "05/14/2021",
        "from_time": "12:00 AM",
        "to_date": "05/16/2021",
        "to_time": "11:59 PM",
        "tzID": "America/Los_Angeles"
    }

#### Response

    {
        "order_summary": {
            "orders_completed": 743,
            "total_selling_price": "14,283.76",
            "total_tax": "771.35",
            "total_revenue": "15,394.61",
            "total_revenue_cash": "6,777.85",
            "total_revenue_card": "8,616.76",
            "total_lottery_sales": "1,743.00",
            "total_scratchers_sales": "1,491.00"
        }
    }

---

## *Product sales*

### **POST** `/api/v3/reports/product-sales`

Product sales between a time period.

#### Request

    {
        "from_date": "05/14/2021",
        "from_time": "12:00 AM",
        "to_date": "05/16/2021",
        "to_time": "11:59 PM",
        "tzID": "America/Los_Angeles"
    }

#### Response

    {
        "product_sales": [
            {
                "product_name": "Lotto",
                "quantity": "843",
                "selling_price": "803.00"
            },
            {
                "product_name": "Paper Bag",
                "quantity": "106",
                "selling_price": "24.25"
            },
            {
                "product_name": "Unknown",
                "quantity": "56",
                "selling_price": "580.67"
            },
            {
                "product_name": "NONTAX",
                "quantity": "29",
                "selling_price": "512.58"
            }
        ]
    }

---

## *Transactions*

### **POST** `/api/v3/reports/transactions`

Transactions between a time period.

#### Request

    {
        "from_date": "05/14/2021",
        "from_time": "12:00 AM",
        "to_date": "05/16/2021",
        "to_time": "11:59 PM",
        "tzID": "America/Los_Angeles"
    }

#### Response

    {
        "transaction_history": [
            {
                "id": 1234,
                "order_uuid": "3705001405210031",
                "amount": 0.25,
                "status": "Complete",
                "tms": "2021-05-14 00:05:38",
                "user_id": 31,
                "payment_mode": "Cash",
                "clerk": "John Doe"
            },
            {
                "id": 2345,
                "order_uuid": "3130081405210031",
                "amount": 10.89,
                "status": "Complete",
                "tms": "2021-05-14 08:35:42",
                "user_id": 31,
                "payment_mode": "Cash",
                "clerk": "John Doe"
            },
            {
                "id": 3456,
                "order_uuid": "4835081405210031",
                "amount": 2.76,
                "status": "Complete",
                "tms": "2021-05-14 08:36:11",
                "user_id": 31,
                "payment_mode": "Card",
                "clerk": "John Doe"
            }
        ]
    }

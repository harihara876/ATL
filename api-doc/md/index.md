# **Plat4m API Documentation**

List of APIs to interact with **Plat4m** server.

---

## Conventions

* All API calls are performed over HTTPS.
* All calls require input of type `application/json`, unless otherwise indicated.
* All calls return output of type `application/json`, unless otherwise indicated.
* HTTP methods:
    * `GET`: Get existing object
        + Input:  None
        + Output: Requested object in JSON format
    * `PATCH`: Partial update
        + Input:  Changed attributes of an existing object
        + Output: Updated object in JSON format.
    * `POST`: Create new object
        + Input:  New object's attributes
        + Output: Newly created object in JSON format.
    * `DELETE`: Delete existing object
        + Input:  None
        + Output: None
* JWTs are used for authentication.
* Dates are in `YYYY-MM-DD HH:MM:SS` format.

---

## Error Handling

Errors are returned as JSON payload with respective HTTP status codes.
For example:

    {
        "error": {
            "code": "err_account_exists",
            "message": "Email already exists",
            "trace_id": "2dceee25ed1d8ab6"
        }
    }

HTTP status codes have the usual meaning:

Code | Reason
---- | ------
400  | Action failed due to invalid user input.
401  | Not logged in, or not verified.
403  | Insufficient privileges, authentication required.
404  | Entity the action is applied to is not found.
500  | Internal server error.
503  | Temporarily unavailable, retry later.

[Click here to see the list of all custom error codes.](/api-doc/error-codes.html)

---

## Servers

Environment | URL
----------- | -----------------------------
Development | `https://dev.plat4minc.com`
Production  | `https://mystore.plat4minc.com`

Make sure always using the right environment.

---

## Custom Headers

Header                     | Description
-------------------------- | ------------------------------
`X-Plat4m-App-Name`        | Application package name to differentiate Google Play Store and Clover Store apps.
`X-Plat4m-App-Instance-Id` | Unique ID to identify the device.
`X-Plat4m-App-Device`      | Device or host metadata. E.g. Model, Brand
`X-Plat4m-App-Version`     | Application version.
`X-Plat4m-App-Platform`    | Platform metadata. E.g. Android version.

**Note:** Application package names are **constants**. For example `com.plat4minc.mystore` is for Google Play Store and `com.plat4minc.mystore.clover` is for Clover Store.

---

## APIs

Resource                                    | Description
------------------------------------------- | ------------------------------------
[Admin](/api-doc/admin.html)                | APIs related to Admin.
[Cashier](/api-doc/cashier.html)            | APIs related to Cashier.
[User](/api-doc/user.html)                  | APIs common to Admin and Cashier.
[Authentication](/api-doc/auth.html)        | APIs related to authentication.
[Categories](/api-doc/categories.html)      | APIs related to categories, subcategories and sub-subcategories.
[Product](/api-doc/product.html)            | APIs related to products, temporary and temporary2 products.
[Transaction](/api-doc/transaction.html)    | APIs related to transactions (orders).
[Reports](/api-doc/reports.html)            | APIs related to reports, summary etc.

---

## Limitations

* Same email cannot be used as both Owner and Cashier.

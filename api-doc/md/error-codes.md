# Custom Error Codes

List of custom error codes sent by the server.

Code                          | Description
----------------------------- | ------------
`err_missing_header`          | Missing header.
`err_unauthorized`            | Unauthorized user.
`err_forbidden`               | Forbidden.
`err_bad_request`             | Bad request. Required data is not as expected.
`err_method_not_allowed`      | Method not allowed. Must use proper HTTP method.
`err_resource_not_found`      | Resource not found.
`err_internal_server_error`   | Internal server error.
`err_validation`              | Validation errors.
`err_account_exists`          | Account already exists.
`err_false_admin`             | False admin.
`err_false_cashier`           | False cashier.
`err_cashiers_limit_exceeded` | Cashiers limit exceeded. Cannot create more cashiers.
`err_max_logins_reached`      | Maximum number of logins reached.
`err_invalid_token`           | Invalid token.
`err_user_not_found`          | User not found.
`err_order_not_found`         | Order not found.
`err_upc_not_found`           | UPC not found.
`err_product_not_found`       | Product not found.
`err_invalid_otp`             | Invalid OTP.
`err_update_product_failed`   | Failed to update product.
`err_loggedin_other_device`   | Session Time Out

Clients can make use of these codes and make a map of error code and user friendly error messages.

# List of APIs

## **Users & Authentication**
---

## Owner
* POST /api/v3/owner/create
* GET /api/v3/owner/profile
* PATCH /api/v3/owner/update

## Cashier
* POST /api/v3/cashier/create
* GET /api/v3/cashier/profile
* PATCH /api/v3/cashier/update
* GET /api/v3/cashiers
* DELETE /api/v3/cashier/{id}/delete

## Authentication
* POST /api/v3/auth/login

## Password
* POST /api/v3/password/reset/request
* POST /api/v3/password/reset/redeem

## **Store**
---

## Categories
* GET /api/v3/store/categories
* GET /api/v3/store/subcategories
* GET /api/v3/store/subsubcategories

## Products
* GET /api/v3/store/product/upc/{upc}/check
* GET /api/v3/store/products
* POST /api/v3/store/product/create
* PATCH /api/v3/store/product/update
* POST /api/v3/store/product/image/upload
* GET /api/v3/store/temp-products
* POST /api/v3/store/temp-product
* GET /api/v3/store/temp-products-2
* POST /api/v3/store/temp-product-2
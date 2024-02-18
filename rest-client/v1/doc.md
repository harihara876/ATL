# **Plat4m** API Documentation

Restful APIs to communicate with Plat4m server.

---------------

## **Endpoints**

Endpoints (URLs) for different environments.

Endpoint    | URL
------------|-----------------------------------
Development | `http://dev.plat4minc.com/api`
Production  | `http://mystore.plat4minc.com/api`

---------------

## **Create Admin User**

Creates a new Admin.

* **Method:**

    `POST`

* **URL:**

    `/api/create-admin-user`

* **Headers:**

  * `Content-Type: application/json`
  * `Authorization: Bearer TOKEN`

* **Request Body:**

    ```bash
    {
        "name": "NAME",
        "email": "EMAIL",
        "password": "PASSWORD"
    }
    ```

    Parameter     | Data Type | Requirement | Description
    --------------|-----------|-------------|----------------------
    **name**      | String    | Required    | Name of the admin.
    **email**     | String    | Required    | Email of the admin.
    **password**  | String    | Required    | Password for the account.

* **Response:**

    ```bash
    {
        "error": false,
        "message": "MESSAGE"
    }
    ```

---------------

## **Create Device User**

Creates a new Device user.

* **Method:**

    `POST`

* **URL:**

    `/api/create-device-user`

* **Headers:**

    `Content-Type: application/json
    Authorization: Bearer TOKEN`

* **Request:**

    ```bash
    {
        "name": "NAME",
        "email": "EMAIL",
        "password": "PASSWORD"
    }
    ```

    Parameter     | Data Type | Requirement | Description
    --------------|-----------|-------------|----------------------
    **name**      | String    | Required    | Name of the user.
    **email**     | String    | Required    | Email of the user.
    **password**  | String    | Required    | Password for the account.

* **Response:**

    ```bash
    {
        "error": false,
        "message": "MESSAGE"
    }
    ```

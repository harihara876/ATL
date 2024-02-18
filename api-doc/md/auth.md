# Authentication

JWTs are used for authentication. APIs which require authorized user expects JWT in `Authorization` header. JWTs are issued when user is authenticated successfully.

Server expects JWTs in below format:

> `Bearer eyJhbGciOiJIUzI1NiIsInR5...`

Below attributes are expected in response payload to maintain sessions.

Attribute       | Type   | Editable | Description
--------------- | ------ | -------- | --------------------------
`access_token`  | string | no       | Access token (JWT) used to maintain session.
`refresh_token` | string | no       | Refresh token (JWT) used to fetch new pair of access and refresh tokens

---

## *Admin or Cashier Login*

### **POST** `/api/v3/auth/login`

Authenticates user and issues JWTs to maintain sessions.

#### Request

    {
        "email": "john.doe@domain.com",
        "password": "J0hnDoe@123"
    }

> **Mandatory**: `email, password`.

#### Response

    {
        "id": 456,
        "first_name": "John",
        "last_name": "Doe",
        "email": "john.doe@domain.com",
        "mobile_number": "+918987678732",
        "admin_id": 123,
        "role": "cashier",
        "paytm_credentials": {
            "merchant_id": "LUfX+OlJ0co7QGB5...",
            "merchant_key": "NlAg+8Jwt3RtDrPDo..."
        },
        "currency": "USD",
        "currency_symbol": "$",
        "access_token": "eyJhbGciOiJIUzI1NiIsInR5...",
        "refresh_token": "eyJhbGciOiJIUzI1NiIsInR5..."
    }

---

## *Refresh Tokens*

### **GET** `/api/v3/auth/refresh`

Issues new pair of access and refresh tokens. Current refresh token is expected in `Authorization` header.

#### Request

    Not required.


#### Response

    {
        "access_token": "eyJhbGciOiJIUzI1NiIsInR5...",
        "refresh_token": "eyJhbGciOiJIUzI1NiIsInR5..."
    }

---

## *Admin or Cashier Logout*

### **GET** `/api/v3/auth/logout`

Clears sessions of Admin or Cashier based on access token.

#### Request

    Not required.

#### Response

    Not required.

---

## *Admin or Cashier Force Login Request*

### **POST** `/api/v3/auth/login/force/request`

Sends an OTP to the registered email to verify if admin or cashier has right to force login.

#### Request

    {
        "email": "john.doe@domain.com"
    }

> **Mandatory**: `email`.

#### Response

    {
        "message": "An OTP has sent to the registered email. Use the OTP to force login."
    }

---

## *Admin or Cashier Force Login*

### **POST** `/api/v3/auth/login/force`

Authenticates user using the OTP, removes all other sessions except the current session and issues JWTs to maintain new sessions.

#### Request

    {
        "email": "john.doe@domain.com",
        "otp": "8098"
    }

> **Mandatory**: `email, otp`.

#### Response

    {
        "id": 456,
        "first_name": "John",
        "last_name": "Doe",
        "email": "john.doe@domain.com",
        "mobile_number": "+918987678732",
        "admin_id": 123,
        "role": "cashier",
        "paytm_credentials": {
            "merchant_id": "LUfX+OlJ0co7QGB5...",
            "merchant_key": "NlAg+8Jwt3RtDrPDo..."
        },
        "currency": "USD",
        "currency_symbol": "$",
        "access_token": "eyJhbGciOiJIUzI1NiIsInR5...",
        "refresh_token": "eyJhbGciOiJIUzI1NiIsInR5..."
    }

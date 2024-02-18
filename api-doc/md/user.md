# User

User refers to Admin or Cashier. See `Admin` or `Cashier` pages for more information.

---

## *Reset Password Request*

### **POST** `/api/v3/user/password/reset/request`

Password reset request for Admin or Cashier.

An OTP will be sent to the requested email.

#### Request

    {
        "email": "john.doe@domain.com"
    }

#### Response

    {
        "message": "An OTP has sent to the registered email. Reset your password using the OTP."
    }

---

## *Reset Password*

### **POST** `/api/v3/user/password/reset`

Reset password uisng OTP.

#### Request

    {
        "otp": "2312",
        "password": "new password"
    }

#### Response

    {
        "message": "Password updated successfully"
    }

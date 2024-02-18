<?php
// Config for LOCAL environment.
// This file contains configuration related to various services
// or resources used in the entire application.

// DB (AWS RDS) configuration.
define("DB_HOST", "localhost"); // Host.
define("DB_PORT", "3306"); // Port.3306
define("DB_USERNAME", "awg825y6_plat4m"); // Username.-:awg825y6_plat4m
define("DB_PASSWORD", "He@ding@t4OOkmph"); // Password.-:He@ding@t4OOkmph
define("DB_NAME", "awg825y6_plat4m"); // DB name.

// Email configuration.
define("EMAIL_SMTP_HOST", "mail.plat4minc.com"); // SMTP host.
define("EMAIL_SMTP_PORT", 587); // SMTP port.
define("EMAIL_SENDER", "support@plat4minc.com"); // Sender email.
define("EMAIL_SENDER_NAME", "Plat4m Inc. - Support"); // Sender name.
define("EMAIL_SENDER_PASSWORD", ",2=Z4(wAP4,["); // Sender password.
define("EMAIL_SMTP_SECURITY", "tls");

// URL configuration.
define("URL_HOST", "http://plat4m.local");

// JWT configuration.
define("JWT_ISSUER", "plat4m.local");
define("JWT_SECRET", "12cde6e27c884eac1cd13f96858c1957fd4144079744f103");
define("JWT_SIGN_ALGO", "HS512");
define("JWT_ACCESS_TOKEN_EXPIRY_SECS", 1800);
define("JWT_REFRESH_TOKEN_EXPIRY_SECS", 172800);

// Weather API configuration.
define("WEATHER_API_KEY", "08373d7cbbd06cda2baa7806008b498a");
define("WEATHER_API_URL", "http://api.openweathermap.org/data/2.5/weather");
define("WEATHER_API_PARAMS", [
    "appid"     => WEATHER_API_KEY,
    "lat"       => NULL,
    "lon"       => NULL,
]);

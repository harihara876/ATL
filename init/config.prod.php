<?php

// Config for PRODUCTION environment.
// This file contains configuration related to various services
// or resources used in the entire application.

// DB (AWS RDS) configuration.
define("DB_HOST", "plat4minc.cnf2nhyetoks.us-west-1.rds.amazonaws.com"); // Host.
define("DB_PORT", "3306"); // Port.
define("DB_USERNAME", "plat4minc"); // Username.
define("DB_PASSWORD", "ZzFF2jaWzBgrHfKU"); // Password.
define("DB_NAME", "awg825y6_plat4m"); // DB name.

// Email configuration.
define("EMAIL_SMTP_HOST", "mail.plat4minc.com"); // SMTP host.
define("EMAIL_SMTP_PORT", 587); // SMTP port.
define("EMAIL_SENDER", "support@plat4minc.com"); // Sender email.
define("EMAIL_SENDER_NAME", "Plat4m Inc. - Support"); // Sender name.
define("EMAIL_SENDER_PASSWORD", ",2=Z4(wAP4,["); // Sender password.
define("EMAIL_SMTP_SECURITY", "tls");

// URL configuration.
define("URL_HOST", "https://mystore.plat4minc.com");

// JWT configuration.
define("JWT_ISSUER", "mystore.plat4minc.com");
define("JWT_SECRET", "d24b99f40920823449c1df38fb1f90d180213491d7ecfaf4");
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

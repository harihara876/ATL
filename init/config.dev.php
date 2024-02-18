<?php

// Config for DEVELOPMENT environment.
// This file contains configuration related to various services
// or resources used in the entire application.

// DB (AWS RDS) configuration.
define("DB_HOST", "database-1.ccoqixglkw7l.us-east-2.rds.amazonaws.com"); // Host.
define("DB_PORT", "3306"); // Port.
define("DB_USERNAME", "awg825y6_plat4m"); // Username.
define("DB_PASSWORD", "Plat4minc"); // Password.
define("DB_NAME", "awg825y6_plat4m"); // DB name.

// Email configuration.
define("EMAIL_SMTP_HOST", "mail.plat4minc.com"); // SMTP host.
define("EMAIL_SMTP_PORT", 587); // SMTP port.
define("EMAIL_SENDER", "support@plat4minc.com"); // Sender email.
define("EMAIL_SENDER_NAME", "Plat4m Inc. - Support"); // Sender name.
define("EMAIL_SENDER_PASSWORD", ",2=Z4(wAP4,["); // Sender password.
define("EMAIL_SMTP_SECURITY", "tls");

// URL configuration.
define("URL_HOST", "https://dev.plat4minc.com");

// JWT configuration.
define("JWT_ISSUER", "dev.plat4minc.com");
define("JWT_SECRET", "1308fdef2762b270aa8a335d23220102c3fd045c08102374");
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

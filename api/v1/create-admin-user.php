<?php

require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Core\API\DeviceUser;
use Plat4m\Utilities\Logger;
use Plat4m\Utilities\Request;
use Plat4m\Utilities\Response;

try {
	Middleware::verifyAuth();

	Logger::httpMsg();
	$userInfo = Request::payload();

	$db = (new DB)->getConn();

	$deviceUser = (new DeviceUser($db))->getInfoByEmail($userInfo['email']);
	$adminUser = (new DeviceUser($db))->getAdminInfoByEmail($userInfo['email']);

	if (!isset($deviceUser["email"]) && !isset($adminUser["email"])) {
		$adminUserId = (new DeviceUser($db))->insertAdminUser(
			$userInfo['name'],
			$userInfo['email'],
			$userInfo['password']
		);

		if ($adminUserId) {
			$response = [
				"id"          => $adminUserId,
				"error"       => FALSE,
				"message"     => "Successfully created",
			];
		} else {
			$response = [
				"error"        => TRUE,
				"message"      => "Unable to create",
			];
		}
	} else {
		$response = [
			"error"        => TRUE,
			"message"      => "Email already Exist",
		];
	}

	Response::statusCode(200)::body($response)::json();
} catch (Exception $ex) {
	Response::statusCode($ex->getCode())::body([
		"error"     => TRUE,
		"message"   => $ex->getMessage()
	])::json();
}

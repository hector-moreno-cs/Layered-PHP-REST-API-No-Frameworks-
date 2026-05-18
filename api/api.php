<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../api-logic/router.php';
require_once __DIR__ . '/../api-logic/database.php';
require_once __DIR__ . '/../api-logic/logger.php';
// Device Types
require_once __DIR__ . '/../api-logic/Controllers/device_types_controller.php';
require_once __DIR__ . '/../api-logic/ApplicationLayer/device_types_service.php';
require_once __DIR__ . '/../api-logic/DataAccessLayer/device_types_data.php';
//Manufacturers
require_once __DIR__ . '/../api-logic/Controllers/manufacturers_controller.php';
require_once __DIR__ . '/../api-logic/ApplicationLayer/manufacturers_service.php';
require_once __DIR__ . '/../api-logic/DataAccessLayer/manufacturers_data.php';
//Equipment/Devices
require_once __DIR__ . '/../api-logic/Controllers/devices_controller.php';
require_once __DIR__ . '/../api-logic/ApplicationLayer/devices_service.php';
require_once __DIR__ . '/../api-logic/DataAccessLayer/devices_data.php';


try {
	$dblink = new Database("Equipment");
	$loggerLink = new Database("Logging");
	$logger = new Logger($loggerLink->getConnection());
	
	$deviceTypesData = new DeviceTypesData($dblink->getConnection());
	$deviceTypesService = new DeviceTypesService($deviceTypesData, $logger);
	$deviceTypesController = new DeviceTypesController($deviceTypesService);
	
	$manufacturersData = new ManufacturersData($dblink->getConnection());
	$manufacturersService = new ManufacturersService($manufacturersData, $logger);
	$manufacturersController = new ManufacturersController($manufacturersService);
	
	$devicesData = new DevicesData($dblink->getConnection());
	$devicesService = new DevicesService($devicesData, $manufacturersData, $deviceTypesData, $logger);
	$devicesController = new DevicesController($devicesService);
	
	$router = new Router($deviceTypesController, $manufacturersController, $devicesController);
	echo $router->handleRequest();
}
catch (Exception $exception) {
	echo json_encode(["status" => "error", "message" => "Something went wrong with the API. Try again", "data" => null]);
	exit;
}

die()
?>
<?php

class Router {

    private $deviceTypesController;
	private $manufacturersController;
	private $devicesController;

    public function __construct($deviceTypesController, $manufacturersController, $devicesController) {
        $this->deviceTypesController = $deviceTypesController;
		$this->manufacturersController = $manufacturersController;
		$this->devicesController = $devicesController;
    }

    public function handleRequest() {
		$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$urlArray = explode("/", trim($url, "/"));
		$endpoint = $urlArray[1] ?? null;

		try {
			switch ($endpoint) {
				
				case "add_device_type":					
					return json_encode($this->deviceTypesController->addDeviceType());
				case "modify_device_type":
					return json_encode($this->deviceTypesController->modifyDeviceType());
				case "get_active_device_types":					
					return json_encode($this->deviceTypesController->getActiveDeviceTypes());
				case "get_all_device_types":
					return json_encode($this->deviceTypesController->getAllDeviceTypes());
				
				case "add_manufacturer":
					return json_encode($this->manufacturersController->addManufacturer());
				case "modify_manufacturer":
					return json_encode($this->manufacturersController->modifyManufacturer());
				case "get_active_manufacturers":
					return json_encode($this->manufacturersController->getActiveManufacturers());
				case "get_all_manufacturers":
					return json_encode($this->manufacturersController->getAllManufacturers());
					
				case "add_device":
					return json_encode($this->devicesController->addDevice());
				case "modify_device":
					return json_encode($this->devicesController->modifyDevice());
				case "get_device_for_view":
					return json_encode($this->devicesController->getDeviceForView());
				case "get_all_devices":
					return json_encode($this->devicesController->getAllDevices());
				case "search_by_serial":
					return json_encode($this->devicesController->searchBySerial());
				case "search_by_status":
					return json_encode($this->devicesController->searchByStatus());
				case "search_by_device_type":
					return json_encode($this->devicesController->searchByCriteria());
				case "search_by_manufacturer":
					return json_encode($this->devicesController->searchByCriteria());

				default:
					return json_encode($this->response("error", "Invalid endpoint", $endpoint));
			}

		} 
		catch (Exception $exception) {
			return json_encode($this->response("error", $exception->getMessage()));
		}
	}

    private function response($status, $message, $data = []) {
        return ["status" => $status, "message" => $message, "data" => $data];
    }
}
?>
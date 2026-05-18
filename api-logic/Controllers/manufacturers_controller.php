<?php

class ManufacturersController {

    private $manufacturersService;

    public function __construct($manufacturersService) {
        $this->manufacturersService = $manufacturersService;
    }

    public function getActiveManufacturers() {
		
		$result = $this->manufacturersService->getActiveManufacturers();
		if ($result['success'] === true) {
			return $this->success("Active manufacturers retrieved", $result['data']);
		}
		
		return $this->error($result['error_message'], $result['data']);
		
    }
	
	public function getAllManufacturers() {
		
		$result = $this->manufacturersService->getAllManufacturers();
		if ($result['success'] === true) {
			return $this->success("Manufacturers retrieved", $result['data']);
		}
		
		return $this->error($result['error_message'], $result['data']);
		
    }
	
	public function addManufacturer() {
		$newManufacturer = trim($_REQUEST['newManufacturer'] ?? '');
		
		if ($newManufacturer === '') return $this->error("Manufacturer name is empty", "EMPTY_NAME");
		
		$result = $this->manufacturersService->addManufacturer($newManufacturer);
		
		if ($result['success'] ===  true) 
			return $this->success("New manufacturer added");
		
		
		return $this->error($result['error_message'], $result['data']);	
	}
	
	public function modifyManufacturer() {
		$modifySelection = (int)($_REQUEST['manufacturerId'] ?? 0);
		$newName = trim($_REQUEST['newManufacturerName'] ?? '');
		$newStatus = trim($_REQUEST['newManufacturerStatus'] ?? '');
		
		if ($modifySelection === 0) return $this->error("Invalid manufacturer selection", "INVALID_SELECTION");
		if ($newName === '') return $this->error("Manufacturer name is empty", "EMPTY_NAME");
		if ($newStatus === '') return $this->error("Status input is empty", "EMPTY_STATUS");
		
		$result = $this->manufacturersService->modifyManufacturer($modifySelection, $newName, $newStatus);
		
		if ($result['success'] ===  true) 
			return $this->success("Manufacturer modified");
		
		return $this->error($result['error_message'], $result['data']);
		
	}
	
	private function success($message, $data = null) {
		return ["status" => "success", "message" => $message, "data" => $data];
	}
	
	private function error($error_message, $data = null) {
		return ["status" => "error", "message" => $error_message, "data" => $data];
	}
}
?>
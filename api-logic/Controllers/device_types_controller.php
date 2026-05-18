<?php

class DeviceTypesController {

    private $deviceTypesService;

    public function __construct($deviceTypesService) {
        $this->deviceTypesService = $deviceTypesService;
    }

    public function getActiveDeviceTypes() {
		
		$result = $this->deviceTypesService->getActiveDeviceTypes();
		if ($result['success'] === false) {
			return $this->error($result['error_message'], $result['data']);
		}
		
		return $this->success("Active device types retrieved", $result['data']);
		
    }
	
	public function getAllDeviceTypes() {
		
		$result = $this->deviceTypesService->getAllDeviceTypes();
		if ($result['success'] === false) {
			return $this->error($result['error_message'], $result['data']);
		}
		return $this->success("Device types retrieved", $result['data']);
	}
	
	public function addDeviceType() {
		$newDeviceType = trim($_REQUEST['newDeviceType'] ?? '');
		
		if ($newDeviceType === '') return $this->error("Device type name is empty", "EMPTY_NAME");
		$result = $this->deviceTypesService->addDeviceType($newDeviceType);
		
		if ($result['success'] ===  true) 
			return $this->success("New device type added");
		
		
		return $this->error($result['error_message'], $result['data']);	
	}
	
	public function modifyDeviceType() {
		$modifySelection = (int)($_REQUEST['deviceTypeId'] ?? 0);
		$newName = trim($_REQUEST['newDeviceTypeName'] ?? '');
		$newStatus = trim($_REQUEST['newDeviceTypeStatus'] ?? '');
		
		if ($modifySelection === 0) return $this->error("Invalid device type selection", "INVALID_SELECTION");
		if ($newName === '') return $this->error("Device type name is empty", "EMPTY_NAME");
		if ($newStatus === '') return $this->error("Status input is empty", "EMPTY_STATUS");
		
		$result = $this->deviceTypesService->modifyDeviceType($modifySelection, $newName, $newStatus);
		
		if ($result['success'] ===  true) 
			return $this->success("Device type modified");
		
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
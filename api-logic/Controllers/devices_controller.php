<?php

class DevicesController {
	
	private $devicesService;
	
	public function __construct($devicesService) {
		$this->devicesService = $devicesService;
	}
	
	public function searchBySerial() {
		$serialSelection = trim($_REQUEST['serialNumber'] ?? '');
		if ($serialSelection == '')			
			return $this->error("Serial number is empty", "EMPTY_SERIAL");
		
		$result = $this->devicesService->searchBySerial($serialSelection);
		
		if ($result['success'] === true) {
			return $this->success("Device retrieved", $result['data']);
		}
		
		return $this->error($result['error_message'], $result['data']);
		
	}
	
	public function getAllDevices() {
		
		if (isset($_REQUEST['last_id'])) $lastId = (int)$_REQUEST['last_id'];
		else $lastId = null;
		if (isset($_REQUEST['first_id'])) $firstId = (int)$_REQUEST['first_id'];
		else $firstId = null;
		$result = $this->devicesService->getAllDevices($lastId, $firstId);
		
		if ($result['success'] === false) {
			return $this->error($result['error_message'], $result['data']);
		}
		
		return $this->success("Devices retrieved", $result['data']);
	}
	
	public function getDeviceForView() {
		$serialSelection = trim($_REQUEST['sn'] ?? '');
		if ($serialSelection == '')			
			return $this->error("Serial number is empty", "EMPTY_SERIAL");
		
		$result = $this->devicesService->getDeviceForView($serialSelection);
		
		if ($result['success'] === true) {
			return $this->success("Devices retrieved", $result['data']);
		}
		
		return $this->error($result['error_message'], $result['data']);
	}
	
	public function searchByCriteria() {
		
		$deviceRaw = $_REQUEST['deviceType'] ?? ''; 
		$manufacturerRaw   = $_REQUEST['manufacturer'] ?? '';
		if (isset($_REQUEST['last_id'])) $lastId = (int)$_REQUEST['last_id'];
		else $lastId = null;
		if (isset($_REQUEST['first_id'])) $firstId = (int)$_REQUEST['first_id'];
		else $firstId = null;
		
		if ($deviceRaw === '') 
			return $this->error("Device type is empty", "EMPTY_TYPE");
					
		if ($manufacturerRaw === '') 
			return $this->error("Manufacturer is empty", "EMPTY_MANUFACTURER");
		
		if ($deviceRaw === 'devicesAll') $deviceSelection = 'devicesAll';
		else $deviceSelection = (int)$deviceRaw;

		if ($manufacturerRaw === 'manuAll') $manufacturerSelection = 'manuAll';
		else $manufacturerSelection = (int)$manufacturerRaw;
		
		$result = $this->devicesService->searchByCriteria($deviceSelection, $manufacturerSelection, $lastId, $firstId);
		
		if ($result['success'] === true) {
			return $this->success("Devices retrieved", $result['data']);
		}
		
		return $this->error($result['error_message'], $result['data']);
	}
	
	public function searchByStatus() {
		$statusSelection = $_REQUEST['deviceStatus'] ?? '';
		if (isset($_REQUEST['last_id'])) $lastId = (int)$_REQUEST['last_id'];
		else $lastId = null;
		if (isset($_REQUEST['first_id'])) $firstId = (int)$_REQUEST['first_id'];
		else $firstId = null;
		
		$result = $this->devicesService->searchByStatus($statusSelection, $lastId, $firstId);
		
		if ($result['success'] === true) {
			return $this->success("Devices retrieved", $result['data']);
		}
		
		return $this->error($result['error_message'], $result['data']);
	}
	
	public function addDevice() {
		$deviceTypeSelection = (int)($_REQUEST['deviceType'] ?? 0);
        $manufacturerSelection = (int)($_REQUEST['manufacturer'] ?? 0);
        $serialSelection = trim($_REQUEST['serialNumber'] ?? '');
		
		if ($serialSelection === '') return $this->error("Serial number is empty", "EMPTY_SERIAL");
		
		$result = $this->devicesService->addDevice($deviceTypeSelection, $manufacturerSelection, $serialSelection);
		
		if ($result['success'] === true) {
			return $this->success("New device added");
		}
		
		return $this->error($result['error_message'], $result['data']);
		
	}
	
	public function modifyDevice() {
		
		$modifyDeviceSelection = trim($_REQUEST['oldSerial'] ?? '');
		$newDeviceTypeSelection = (int)($_REQUEST['newDeviceType'] ?? 0);
		$newManufacturerSelection = (int)($_REQUEST['newManufacturer'] ?? 0);
		$newSerialSelection = trim($_REQUEST['newSerialNumber'] ?? '');
		$newStatus = $_REQUEST['newDeviceStatus'] ?? '';
		
		if ($modifyDeviceSelection === '')	return $this->error("Device to modify is missing the serial number input", "EMPTY_OLD_SERIAL");
		if ($newSerialSelection === '')	return $this->error("New serial number input is empty", "EMPTY_NEW_SERIAL");
		if ($newStatus === '')	return $this->error("Status is empty", "EMPTY_STATUS");
		
		$result = $this->devicesService->modifyDevice($modifyDeviceSelection, $newDeviceTypeSelection, $newManufacturerSelection, $newSerialSelection, $newStatus);
		
		if ($result['success'] === true) {
			return $this->success("Device modified");			
		}
			
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
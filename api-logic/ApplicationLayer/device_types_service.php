<?php

class DeviceTypesService {

    private $deviceTypesData;
	private $logger;
	private $serviceType = "DeviceTypesService";
	private $status = ['active' => true, 'inactive' => true];

    public function __construct($deviceTypesData, $logger) {
        $this->deviceTypesData = $deviceTypesData;
		$this->logger = $logger;
    }

    public function getActiveDeviceTypes() {
		
		try {
			$result = $this->deviceTypesData->getActiveDeviceTypes();
			if (empty($result)) {
				
				$this->logger->log("ERROR", "No active device types", "EMPTY_SEARCH", $this->serviceType);
				return $this->error("No active device types", "EMPTY_SEARCH");
			}
			return $this->success($result);
		}
		catch (Exception $exception) {
			
			$this->logger->log("ERROR", "Searching active device types error", "SEARCHING_ERROR", $this->serviceType, $exception->getMessage());
			return $this->error("Searching active device types error", "SEARCHING_ERROR");
		}
    }
	
	public function getAllDeviceTypes() {
		
		try {
			$result = $this->deviceTypesData->getAllDeviceTypes();
			if (empty($result)) {
				
				$this->logger->log("ERROR", "No device types", "EMPTY_SEARCH", $this->serviceType);
				return $this->error("No device types", "EMPTY_SEARCH");
			}
			return $this->success($result);
		}
		catch (Exception $exception) {
			
			$this->logger->log("ERROR", "Searching device types error", "SEARCHING_ERROR", $this->serviceType, $exception->getMessage());
			return $this->error("Searching device types error", "SEARCHING_ERROR");
		}
    }
	
	public function addDeviceType($newDeviceType) {
		
		if (!ctype_alpha($newDeviceType)) {
			
			$this->logger->log("ERROR", "Device type name must only contain letters", "INVALID_NAME", $this->serviceType);
			return $this->error("Device type name must only contain letters", "INVALID_NAME");
		}
		if (strlen($newDeviceType) >= 32) {
			
			$this->logger->log("ERROR", "Device type name must be less than 31 characters", "INVALID_NAME", $this->serviceType);
			return $this->error("Device type name must be less than 31 characters", "INVALID_NAME");
		}
		
		try {
			$result = $this->deviceTypesData->addDeviceType(strtolower($newDeviceType));
			if ($result === false) {
				
				$this->logger->log("ERROR", "No new device type added", "INSERTION_ERROR", $this->serviceType);
				return $this->error("No new device type added", "INSERTION_ERROR");
			}
			return $this->success();

		} 
		catch (mysqli_sql_exception $exception) {

			if ($exception->getCode() === 1062) {
				
				$this->logger->log("ERROR", "Device type name already exists in the database", "DUPLICATE_NAME", $this->serviceType, $exception->getMessage());
				return $this->error("Device type name already exists in the database", "DUPLICATE_NAME");
			}
			
			$this->logger->log("ERROR", "Device type insertion error", "INSERTION_ERROR", $this->serviceType, $exception->getMessage());
			return $this->error("Device type insertion error", "INSERTION_ERROR");
		}
	}
	
	public function modifyDeviceType($modifySelection, $newName, $newStatus) {

		$newName = strtolower($newName);
		$newStatus = strtolower($newStatus);

		if (!isset($this->status[$newStatus])) {
			
			$this->logger->log("ERROR", "Invalid status input", "INVALID_STATUS", $this->serviceType);
			return $this->error("Invalid status input", "INVALID_STATUS");
		}
		if (!preg_match('/^[a-zA-Z ]+$/', $newName)) {
			
			$this->logger->log("ERROR", "Invalid device type name", "INVALID_NAME", $this->serviceType);
			return $this->error("Invalid device type name", "INVALID_NAME");
		}
		if (strlen($newName) >= 32) {
			
			$this->logger->log("ERROR", "Device type name must be less than 31 characters", "INVALID_NAME", $this->serviceType);
			return $this->error("Device type name must be less than 31 characters", "INVALID_NAME");
		}

		try {
			
			if (!$this->deviceTypesData->deviceTypeExistsAll($modifySelection)) {
				
				$this->logger->log("ERROR", "Device type selection is not valid", "INVALID_SELECTION", $this->serviceType);
				return $this->error("Device type selection is not valid", "INVALID_SELECTION");
			}
			
			$result = $this->deviceTypesData->modifyDeviceType($newName, $newStatus, $modifySelection);
			if ($result === false) {
				
				$this->logger->log("ERROR", "No new updates made", "NO_UPDATE", $this->serviceType);
				return $this->error("No new updates made", "NO_UPDATE");
			}
			return $this->success();

		} 
		catch (mysqli_sql_exception $exception) {

			if ($exception->getCode() === 1062) {
				$this->logger->log("ERROR", "Device type name already exists in the database", "DUPLICATE_NAME", $this->serviceType, $exception->getMessage());
				return $this->error("Device type name already exists in the database", "DUPLICATE_NAME");
			}
			
			$this->logger->log("ERROR", "Device type modification error", "MODIFICATION_ERROR", $this->serviceType, $exception->getMessage());
			return $this->error("Device type modification error", "MODIFICATION_ERROR");
		}
	}
	
	
	private function success($data = null) {
		return ["success" => true, "data" => $data];
	}
	
	private function error($message, $data = null) {
		return ["success" => false, "error_message" => $message, "data" => $data];
	}
}
?>
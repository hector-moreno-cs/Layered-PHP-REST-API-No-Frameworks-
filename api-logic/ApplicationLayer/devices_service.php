<?php

class DevicesService {

    private $devicesData;
	private $manufacturersData;
	private $deviceTypesData;
	private $logger;
	private $serviceType = "DevicesService";
	private $status = ['active' => true, 'inactive' => true];
	private $statusOptions = ['activeDevices' => 'active', 'inactiveDevices' => 'inactive', 'devicesAll' => true];

    public function __construct($devicesData,$manufacturersData, $deviceTypesData, $logger) {
        $this->devicesData = $devicesData;
		$this->manufacturersData = $manufacturersData;
		$this->deviceTypesData = $deviceTypesData;
		$this->logger = $logger;
    }
	
	public function searchBySerial($serialSelection) {
		
		if (!preg_match('/^SN-([0-9A-Za-z]{64})$/', $serialSelection)) {
			
			$this->logger->log("ERROR", "Invalid serial number", "INVALID_SERIAL", $this->serviceType);
			return $this->error("Invalid serial number", "INVALID_SERIAL");
		}
		
		try {
			
			$result = $this->devicesData->searchBySerial(substr($serialSelection, 3));
			if (empty($result)) {
				
				$this->logger->log("ERROR", "No device found", "SEARCHING_ERROR", $this->serviceType);
				return $this->error("No device found", "SEARCHING_ERROR");
			}
			
			return $this->success($result);

		}
		catch (Exception $exception) {
			
			$this->logger->log("ERROR", "Searching device by serial error", "SEARCHING_ERROR", $this->serviceType, $exception->getMessage());
			return $this->error("Searching device by serial error", "SEARCHING_ERROR");
		}
		
	}
	
	public function getAllDevices($lastId, $firstId) {
		
		try {
			$result = $this->devicesData->getAllDevices(50, $lastId, $firstId);
			if (empty($result)) {
				
				$this->logger->log("ERROR", "No devices found", "SEARCHING_ERROR", $this->serviceType);
				return $this->error("No devices found", "SEARCHING_ERROR");
			}
			return $this->success($result);
		}
		catch (Exception $exception) {
			
			$this->logger->log("ERROR", "Searching devices error", "SEARCHING_ERROR", $this->serviceType, $exception->getMessage());
			return $this->error("Searching devices error", "SEARCHING_ERROR");
		}
    }
	
	public function getDeviceForView($serialSelection) {
		
		if (!preg_match('/^SN-([0-9A-Za-z]{64})$/', $serialSelection)) {
			
			$this->logger->log("ERROR", "Invalid serial number", "INVALID_SERIAL", $this->serviceType);
			return $this->error("Invalid serial number", "INVALID_SERIAL");
		}
		
		try {
			
			$result = $this->devicesData->getDeviceForView(substr($serialSelection, 3));
			if (empty($result)) {
				
				$this->logger->log("ERROR", "No results found for view", "SEARCHING_ERROR", $this->serviceType);
				return $this->error("No results found for view", "SEARCHING_ERROR");
			}
			
			return $this->success($result);

		}
		catch (Exception $exception) {
			
			$this->logger->log("ERROR", "Searching device for view error", "SEARCHING_ERROR", $this->serviceType, $exception->getMessage());
			return $this->error("Searching device for view error", "SEARCHING_ERROR");
		}
	}

    public function searchByCriteria($deviceSelection, $manufacturerSelection, $lastId, $firstId) {
		try {
			
			$validDevice = $deviceSelection === 'devicesAll' || $this->deviceTypesData->deviceTypeExists($deviceSelection); 
			$validManufacturer   = $manufacturerSelection === 'manuAll' || $this->manufacturersData->manufacturerExists($manufacturerSelection); 
			
			if (!$validDevice) {
				
				$this->logger->log("ERROR", "Invalid device type selection", "INVALID_TYPE_SELECTION", $this->serviceType);
				return $this->error("Invalid device type selection", "INVALID_TYPE_SELECTION");
			}
			if (!$validManufacturer) {
				
				$this->logger->log("ERROR", "Invalid manufacturer selection", "INVALID_MANUFACTURER", $this->serviceType);
				return $this->error("Invalid manufacturer selection", "INVALID_MANUFACTURER");
			}
			
			$result = $this->devicesData->searchByCriteria($deviceSelection, $manufacturerSelection, 50, $lastId, $firstId);
			
			if (empty($result)) {
				
				$this->logger->log("ERROR", "No devices found", "SEARCHING_ERROR", $this->serviceType);
				return $this->error("No devices found", "SEARCHING_ERROR");
			}
			
			return $this->success($result);
		}
		catch (Exception $exception) {
			
			$this->logger->log("ERROR", "Searching devices by criteria error", "SEARCHING_ERROR", $this->serviceType, $exception->getMessage());
			return $this->error("Searching devices by criteria error", "SEARCHING_ERROR");
		}
	}
	
	public function searchByStatus($statusSelection, $lastId, $firstId) {
		
		if (!isset($this->statusOptions[$statusSelection])) {
			
			$this->logger->log("ERROR", "Invalid status", "INVALID_STATUS", $this->serviceType);
			return $this->error("Invalid status", "INVALID_STATUS");
		}
		
		try {
			$result = $this->devicesData->searchByStatus($statusSelection, 50, $lastId, $firstId);
			if (empty($result)) {
				
				$this->logger->log("ERROR", "No devices found", "SEARCHING_ERROR", $this->serviceType);
				return $this->error("No devices found", "SEARCHING_ERROR");
			}
			
			return $this->success($result);
		}
		catch(Exception $exception) {
			
			$this->logger->log("ERROR", "Searching devices by status error", "SEARCHING_ERROR", $this->serviceType, $exception->getMessage());
			return $this->error("Searching devices by status error", "SEARCHING_ERROR");
		}
	}
	
	public function addDevice($deviceTypeSelection, $manufacturerSelection, $serialSelection) {
		
		if (!preg_match('/^SN-([0-9A-Za-z]{64})$/', $serialSelection)) {
			
			$this->logger->log("ERROR", "Invalid serial number", "INVALID_SERIAL", $this->serviceType);
			return $this->error("Invalid serial number", "INVALID_SERIAL");
		}

		try {
			if (!$this->deviceTypesData->deviceTypeExists($deviceTypeSelection)) {
				
				$this->logger->log("ERROR", "Device type is not valid or inactive", "INVALID_TYPE_SELECTION", $this->serviceType);
				return $this->error("Device type is not valid or inactive", "INVALID_TYPE_SELECTION");
			}
			if (!$this->manufacturersData->manufacturerExists($manufacturerSelection)) {
				
				$this->logger->log("ERROR", "Manufacturer is not valid or inactive", "INVALID_MANUFACTURER", $this->serviceType);
				return $this->error("Manufacturer is not valid or inactive", "INVALID_MANUFACTURER");
			}
			
			$result = $this->devicesData->addDevice($deviceTypeSelection, $manufacturerSelection, substr($serialSelection, 3));
			
			if ($result === false) {
				
				$this->logger->log("ERROR", "No new device added", "INSERTION_ERROR", $this->serviceType);
				return $this->error("No new device added", "INSERTION_ERROR");
			}
			
			return $this->success();
			
		}
		catch (mysqli_sql_exception $exception) {
			
			if ($exception->getCode() === 1062) {
				
				$this->logger->log("ERROR", "Duplicate serial", "DUPLICATE_SERIAL", $this->serviceType, $exception->getMessage());
				return $this->error("Duplicate serial", "DUPLICATE_SERIAL");
			}
			
			$this->logger->log("ERROR", "Device insertion error", "INSERTION_ERROR", $this->serviceType, $exception->getMessage());
			return $this->error("Device insertion error", "INSERTION_ERROR");
		}
		
		
	}
	
	public function modifyDevice($modifyDeviceSelection, $newDeviceTypeSelection, $newManufacturerSelection, $newSerialSelection, $newStatus) {
		
		$newStatus = strtolower($newStatus);
		
		if (!preg_match('/^SN-([0-9A-Za-z]{64})$/', $modifyDeviceSelection)) {
			
			$this->logger->log("ERROR", "Invalid old serial number", "INVALID_OLD_SERIAL", $this->serviceType);
			return $this->error("Invalid old serial number", "INVALID_OLD_SERIAL");
		}
		
		if (!preg_match('/^SN-([0-9A-Za-z]{64})$/', $newSerialSelection)) {
			
			$this->logger->log("ERROR", "Invalid new serial number", "INVALID_NEW_SERIAL", $this->serviceType);
			return $this->error("Invalid new serial number", "INVALID_NEW_SERIAL");
		}
		
		if (!isset($this->status[$newStatus])) {
			
			$this->logger->log("ERROR", "Invalid status input", "INVALID_STATUS", $this->serviceType);
			return $this->error("Invalid status input", "INVALID_STATUS");
		}
		
		try {
			if (!$this->deviceTypesData->deviceTypeExists($newDeviceTypeSelection)) {
				
				$this->logger->log("ERROR", "Invalid device type", "INVALID_TYPE_SELECTION", $this->serviceType);
				return $this->error("Invalid device type", "INVALID_TYPE_SELECTION");
			}
			if (!$this->manufacturersData->manufacturerExists($newManufacturerSelection)) {
				
				$this->logger->log("ERROR", "Invalid manufacturer", "INVALID_MANUFACTURER_SELECTION", $this->serviceType);
				return $this->error("Invalid manufacturer", "INVALID_MANUFACTURER_SELECTION");
			}
			
			$result = $this->devicesData->modifyDevice(substr($modifyDeviceSelection, 3), $newDeviceTypeSelection, $newManufacturerSelection, substr($newSerialSelection, 3), $newStatus);
			
			if ($result === false) {
				
				$this->logger->log("ERROR", "No new updates made", "NO_UPDATE", $this->serviceType);
				return $this->error("No new updates made", "NO_UPDATE");
			}
			
			return $this->success();
			
		}
		catch (Exception $exception) {
			
			if ($exception->getCode() === 1062) {
				
				$this->logger->log("ERROR", "Duplicate serial", "DUPLICATE_SERIAL", $this->serviceType, $exception->getMessage());
				return $this->error("Duplicate serial", "DUPLICATE_SERIAL");
			}
			
			$this->logger->log("ERROR", "Modification error", "MODIFICATION_ERROR", $this->serviceType, $exception->getMessage());
			return $this->error("Modification error", "MODIFICATION_ERROR");
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
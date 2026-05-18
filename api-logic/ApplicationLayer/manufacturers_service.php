<?php

class ManufacturersService {

    private $manufacturersData;
	private $logger;
	private $serviceType = "ManufacturersService";
	private $status = ['active' => true, 'inactive' => true];

    public function __construct($manufacturersData, $logger) {
        $this->manufacturersData = $manufacturersData;
		$this->logger = $logger;
    }

    public function getActiveManufacturers() {
		
		try {
			$result = $this->manufacturersData->getActiveManufacturers();
			if (empty($result)) {
				
				$this->logger->log("ERROR", "No active manufacturers", "EMPTY_SEARCH", $this->serviceType);
				return $this->error("No active manufacturers", "EMPTY_SEARCH");
			}
			return $this->success($result);
		}
		catch (Exception $exception) {
			
			$this->logger->log("ERROR", "Searching active manufacturers error", "SEARCHING_ERROR", $this->serviceType, $exception->getMessage());
			return $this->error("Searching active manufacturers error", "SEARCHING_ERROR");
		}
    }
	
	public function getAllManufacturers() {
		
		try {
			$result = $this->manufacturersData->getAllManufacturers();
			if (empty($result)) {
				
				$this->logger->log("ERROR", "No manufacturers", "EMPTY_SEARCH", $this->serviceType);
				return $this->error("No manufacturers", "EMPTY_SEARCH");
			}
			return $this->success($result);
		}
		catch (Exception $exception) {
			
			$this->logger->log("ERROR", "Searching manufacturers error", "SEARCHING_ERROR", $this->serviceType, $exception->getMessage());
			return $this->error("Searching manufacturers error", "SEARCHING_ERROR");
		}
    }
	
	public function addManufacturer($newManufacturer) {
		
		if (!preg_match('/^[a-zA-Z ]+$/', $newManufacturer)) {
			
			$this->logger->log("ERROR", "Manufacturer name must only contain letters and spaces", "INVALID_NAME", $this->serviceType);
			return $this->error("Manufacturer name must only contain letters and spaces", "INVALID_NAME");
		}
		if (strlen($newManufacturer) >= 32) {
			
			$this->logger->log("ERROR", "Manufacturer name must be less than 31 characters", "INVALID_NAME", $this->serviceType);
			return $this->error("Manufacturer name must be less than 31 characters", "INVALID_NAME");
		}
		
		try {
			$result = $this->manufacturersData->addManufacturer($newManufacturer);
			if ($result === false) {
				
				$this->logger->log("ERROR", "No new manufacturer added", "INSERTION_ERROR", $this->serviceType);
				return $this->error("No new manufacturer added", "INSERTION_ERROR");
			}
			return $this->success();

		} 
		catch (mysqli_sql_exception $exception) {

			if ($exception->getCode() === 1062) {
				
				$this->logger->log("ERROR", "Manufacturer name already exists in the database", "DUPLICATE_NAME", $this->serviceType, $exception->getMessage());
				return $this->error("Manufacturer name already exists in the database", "DUPLICATE_NAME");
			}
			
			$this->logger->log("ERROR", "Manufacturer insertion error", "INSERTION_ERROR", $this->serviceType, $exception->getMessage());
			return $this->error("Manufacturer insertion error", "INSERTION_ERROR");
		}
	}
	
	public function modifyManufacturer($modifySelection, $newName, $newStatus) {

		if (!isset($this->status[$newStatus])) {
			
			$this->logger->log("ERROR", "Invalid status input", "INVALID_STATUS", $this->serviceType);
			return $this->error("Invalid status input", "INVALID_STATUS");
		}
		if (!ctype_alpha($newName)) {
			
			$this->logger->log("ERROR", "Invalid manufacturer name", "INVALID_NAME", $this->serviceType);
			return $this->error("Invalid manufacturer name", "INVALID_NAME");
		}
		if (strlen($newName) >= 32) {
			
			$this->logger->log("ERROR", "Manufacturer name is greater than 31 characters", "INVALID_NAME", $this->serviceType);
			return $this->error("Manufacturer name is greater than 31 characters", "INVALID_NAME");
		}

		try {
			
			if (!$this->manufacturersData->manufacturerExistsAll($modifySelection)) {
				
				$this->logger->log("ERROR", "Manufacturer is not valid", "INVALID_SELECTION", $this->serviceType);
				return $this->error("Manufacturer is not valid", "INVALID_SELECTION");
			}
			
			$result = $this->manufacturersData->modifyManufacturer($newName, $newStatus, $modifySelection);
			if (!$result) {
				
				$this->logger->log("ERROR", "No new updates made", "NO_UPDATE", $this->serviceType);
				return $this->error("No new updates made", "NO_UPDATE");
			}
			return $this->success();

		} 
		catch (mysqli_sql_exception $exception) {

			if ($exception->getCode() === 1062) {
				
				$this->logger->log("ERROR", "Manufacturer name already exists in the database", "DUPLICATE_NAME", $this->serviceType, $exception->getMessage());
				return $this->error("Manufacturer name already exists in the database", "DUPLICATE_NAME");
			}
			
			$this->logger->log("ERROR", "Manufacturer modification error", "MODIFICATION_ERROR", $this->serviceType, $exception->getMessage());
			return $this->error("Manufacturer modification error", "MODIFICATION_ERROR");
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
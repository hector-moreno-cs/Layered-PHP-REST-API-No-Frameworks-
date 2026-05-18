<?php

class DevicesData {
	
	private $dblink;

    public function __construct($db) {
        $this->dblink = $db;
    }
	
	public function searchBySerial($serialSelection) {
		$stmt = $this->dblink->prepare("
		SELECT d.id AS d_id, dt.name AS dt_name, m.name AS m_name, d.serial_number
		FROM devices d
		JOIN device_types dt ON d.device_type_id = dt.id
		JOIN manufacturers m ON d.manufacturer_id = m.id
		LEFT JOIN inactive_devices ind ON d.id = ind.device_id
		WHERE d.serial_number = ?
		AND ind.device_id IS NULL");

		$stmt->bind_param("s", $serialSelection);
		$stmt->execute();
		$result = $stmt->get_result();
		$rows = [];
		
		if ($row = $result->fetch_assoc()) {
			$rows[] = $row;
		}
		return $rows;
	}
	
	public function getAllDevices($limit = 50, $lastId = null, $firstId = null) {

		$values = [];
		$types = "";

		$sql = "SELECT d.id AS d_id, dt.name AS dt_name, m.name AS m_name, d.serial_number
				FROM devices d
				JOIN device_types dt ON d.device_type_id = dt.id
				JOIN manufacturers m ON d.manufacturer_id = m.id
				WHERE 1=1";

		if ($lastId !== null) {
			$sql .= " AND d.id > ?";
			$values[] = $lastId;
			$types .= "i";
		}
		elseif ($firstId !== null) {
			$sql .= " AND d.id < ?";
			$values[] = $firstId;
			$types .= "i";
		}

		$sql .= " ORDER BY d.id ASC LIMIT ?";
		$values[] = $limit;
		$types .= "i";

		$stmt = $this->dblink->prepare($sql);
		$stmt->bind_param($types, ...$values);

		$stmt->execute();
		$result = $stmt->get_result();

		$data = [];
		while ($row = $result->fetch_assoc()) {
			$data[] = $row;
		}

		return $data;
	}
	
	public function getDeviceForView($serialSelection) {
		$stmt = $this->dblink->prepare("
		SELECT d.id AS d_id, dt.name AS dt_name, m.name AS m_name, d.serial_number,
		CASE 
			WHEN ind.device_id IS NULL THEN 'active'
			ELSE 'inactive'
		END AS device_status
		FROM devices d
		JOIN device_types dt ON d.device_type_id = dt.id
		JOIN manufacturers m ON d.manufacturer_id = m.id
		LEFT JOIN inactive_devices ind ON d.id = ind.device_id
		WHERE d.serial_number = ?");

		$stmt->bind_param("s", $serialSelection);
		$stmt->execute();
		$result = $stmt->get_result();
		
		if ($row = $result->fetch_assoc()) {
			return $row;
		}
		return [];
	}
	
	public function searchByCriteria($deviceType, $manuType, $limit = 50, $lastId = null, $firstId = null) {

		$conditions = [];
		$values = [];
		$types = "";

		if ($deviceType !== "devicesAll") {
			$conditions[] = "d.device_type_id = ?";
			$values[] = $deviceType;
			$types .= "i";
		}

		if ($manuType !== "manuAll") {
			$conditions[] = "d.manufacturer_id = ?";
			$values[] = $manuType;
			$types .= "i";
		}

		$sql = "SELECT dt.name AS dt_name, m.name AS m_name, d.serial_number, d.id
			FROM devices d
			JOIN device_types dt ON d.device_type_id = dt.id
			JOIN manufacturers m ON d.manufacturer_id = m.id
			WHERE dt.status = 'active'
			AND m.status = 'active'";

		if (!empty($conditions)) {
			$sql .= " AND " . implode(" AND ", $conditions);
		}

		if ($lastId !== null) {
			$sql .= " AND d.id > ?";
			$values[] = $lastId;
			$types .= "i";		
		}
		elseif ($firstId !== null) {
			$sql .= " AND d.id < ?";
			$values[] = $firstId;
			$types .= "i";			
		}

		$sql .= " ORDER BY d.id ASC LIMIT ?";
		$values[] = $limit;
		$types .= "i";


		$stmt = $this->dblink->prepare($sql);
		$stmt->bind_param($types, ...$values);

		$stmt->execute();
		$result = $stmt->get_result();

		$data = [];
		while ($row = $result->fetch_assoc()) {
			$data[] = $row;
		}

		return $data;
	}
	
	public function searchByStatus($deviceStatus, $limit = 50, $lastId = null, $firstId = null) {

		$conditions = [];
		$values = [];
		$types = "";

		$sql = "SELECT dt.name AS dt_name, m.name AS m_name, d.serial_number, d.id
			FROM devices d
			JOIN device_types dt ON d.device_type_id = dt.id
			JOIN manufacturers m ON d.manufacturer_id = m.id
			LEFT JOIN inactive_devices ind ON d.id = ind.device_id
			WHERE 1=1";

		if ($deviceStatus === "activeDevices") {
			$conditions[] = "ind.device_id IS NULL";
		}
		elseif ($deviceStatus === "inactiveDevices") {
			$conditions[] = "ind.device_id IS NOT NULL";
		}

		if (!empty($conditions)) {
			$sql .= " AND " . implode(" AND ", $conditions);
		}
		
		if ($lastId !== null) {
			$sql .= " AND d.id > ?";
			$values[] = $lastId;
			$types .= "i";
			
		}
		elseif ($firstId !== null) {
			$sql .= " AND d.id < ?";
			$values[] = $firstId;
			$types .= "i";
			
		}
		
		$sql .= " ORDER BY d.id ASC LIMIT ?";
		$values[] = $limit;
		$types .= "i";

		$stmt = $this->dblink->prepare($sql);
		$stmt->bind_param($types, ...$values);

		$stmt->execute();
		$result = $stmt->get_result();

		$data = [];
		while ($row = $result->fetch_assoc()) {
			$data[] = $row;
		}

		return $data;
	}
	
	public function addDevice($deviceTypeSelection, $manufacturerSelection, $serial) {
		$stmt = $this->dblink->prepare("INSERT INTO devices (device_type_id, manufacturer_id, serial_number) VALUES (?, ?, ?)");
		$stmt->bind_param("iis", $deviceTypeSelection, $manufacturerSelection, $serial);
		$stmt->execute();

		return $stmt->affected_rows === 1;
	}
	
	public function modifyDevice($currentSerial, $newDeviceTypeId, $newManuId, $newSerial, $newStatus) {
		$this->dblink->begin_transaction();

		try {
			$stmt = $this->dblink->prepare("SELECT id FROM devices WHERE serial_number = ?");
			$stmt->bind_param("s", $currentSerial);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows === 0) {
				throw new Exception("Device not found");
			}

			$device = $result->fetch_assoc();
			$deviceId = (int)$device['id'];
			$stmt->close();

			$updStmt = $this->dblink->prepare("UPDATE devices SET device_type_id = ?, manufacturer_id = ?, serial_number = ? WHERE id = ?");
			$updStmt->bind_param("iisi", $newDeviceTypeId, $newManuId, $newSerial, $deviceId);
			$updStmt->execute();
			$updStmt->close();

			
			if ($newStatus === "inactive") {
				$stmt = $this->dblink->prepare("INSERT IGNORE INTO inactive_devices (device_id) VALUES (?)");
				$stmt->bind_param("i", $deviceId);
			} 
			else {
				$stmt = $this->dblink->prepare("DELETE FROM inactive_devices WHERE device_id = ?");
				$stmt->bind_param("i", $deviceId);
			}

			$stmt->execute();
			$stmt->close();

			$this->dblink->commit();
			return true;

		} 
		catch (Exception $exception) {
			$this->dblink->rollback();
			throw $exception; 
		}
	}
	
}

?>
<?php

class DeviceTypesData {

    private $dblink;

    public function __construct($db) {
        $this->dblink = $db;
    }
	
	public function deviceTypeExists($id) {
		$stmt = $this->dblink->prepare("SELECT 1 FROM device_types WHERE id = ? AND status = 'active' LIMIT 1");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		return $stmt->get_result()->num_rows > 0;
	}
	
	public function deviceTypeExistsAll($id) {
		$stmt = $this->dblink->prepare("SELECT 1 FROM device_types WHERE id = ? LIMIT 1");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		return $stmt->get_result()->num_rows > 0;
	}
	
	function getActiveDeviceTypes() {
		$stmt = $this->dblink->prepare("SELECT * FROM device_types WHERE status = ?");
		$status = "active";
		$stmt->bind_param("s", $status);
		$stmt->execute();
		$result = $stmt->get_result();
		
		$data = [];
		while ($row = $result->fetch_assoc()) {
			$data[] = $row;
		}
		
        return $data;
	}

	function getAllDeviceTypes() {
		$stmt = $this->dblink->prepare("SELECT * FROM device_types");
		$stmt->execute();
		$result = $stmt->get_result();
		
		$data = [];
		while ($row = $result->fetch_assoc()) {
			$data[] = $row;
		}
		
        return $data;
	}
	
	public function addDeviceType($name) {
		$stmt = $this->dblink->prepare("INSERT INTO device_types (name, status) VALUES (?, 'active')");
		$stmt->bind_param("s", $name);
		$stmt->execute();

		return $stmt->affected_rows === 1;
	}
	
	public function modifyDeviceType($newName, $newStatus, $deviceTypeSelection){
		$stmt = $this->dblink->prepare("UPDATE device_types SET name = ?, status = ? WHERE id = ?");
		$stmt->bind_param("ssi", $newName, $newStatus, $deviceTypeSelection);
		$stmt->execute();

		return $stmt->affected_rows === 1;
	}
}
?>
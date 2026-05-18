<?php

class ManufacturersData {

    private $dblink;

    public function __construct($db) {
        $this->dblink = $db;
    }
	
	public function manufacturerExists($id) {
		$stmt = $this->dblink->prepare("SELECT 1 FROM manufacturers WHERE id = ? AND status = 'active' LIMIT 1");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		return $stmt->get_result()->num_rows > 0;
	}
	
	public function manufacturerExistsAll($id) {
		$stmt = $this->dblink->prepare("SELECT 1 FROM manufacturers WHERE id = ? LIMIT 1");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		return $stmt->get_result()->num_rows > 0;
	}
	
	function getActiveManufacturers() {
		$stmt = $this->dblink->prepare("SELECT * FROM manufacturers WHERE status = ?");
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

    function getAllManufacturers() {
		$stmt = $this->dblink->prepare("SELECT * FROM manufacturers");
		$stmt->execute();
		$result = $stmt->get_result();
		
		$data = [];
		while ($row = $result->fetch_assoc()) {
			$data[] = $row;
		}
		
        return $data;
	}
	
	public function addManufacturer($name) {
		$stmt = $this->dblink->prepare("INSERT INTO manufacturers (name, status) VALUES (?, 'active')");
		$stmt->bind_param("s", $name);
		$stmt->execute();

		return $stmt->affected_rows === 1;
	}
	
	public function modifyManufacturer($newName, $newStatus, $manufacturerSelection){
		$stmt = $this->dblink->prepare("UPDATE manufacturers SET name = ?, status = ? WHERE id = ?");
		$stmt->bind_param("ssi", $newName, $newStatus, $manufacturerSelection);
		$stmt->execute();

		return $stmt->affected_rows === 1;
	}
}
?>
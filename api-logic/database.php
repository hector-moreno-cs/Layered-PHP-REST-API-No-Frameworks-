<?php

class Database {

    private $dblink;
	private $username="x";
	private $pw="x";
	private $host="localhost";

    public function __construct($db) {
		$this->dblink=new mysqli($this->host, $this->username, $this->pw, $db);

        if ($this->dblink->connect_error) {
            throw new Exception("Database connection failed");
        }
    }
	
	public function getConnection() {		
		return $this->dblink;
	}
	
}

?>

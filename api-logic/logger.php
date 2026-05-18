<?php

class Logger {

    private $dblink;

    public function __construct($db) {
        $this->dblink = $db;
    }

    public function log($level, $message, $code = null, $service = null, $exceptionMessage = null) {
        try {
			$level = strtoupper($level);
			$sql = "INSERT INTO logs (level, message, code, service, exception_message) VALUES (?, ?, ?, ?, ?)";

			$stmt = $this->dblink->prepare($sql);
			$stmt->bind_param("sssss", $level, $message, $code, $service, $exceptionMessage);
			$stmt->execute();

		}
		catch (Exception $exception) {
			error_log($exception->getMessage());
		}
    }
}
?>
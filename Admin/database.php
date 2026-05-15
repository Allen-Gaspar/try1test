<?php

class Database {
    protected $conn;

    public function __construct() {
        $host = "localhost";
        $user = "root";
        $pass = "";
        $dbname = "sis_database"; // Use your actual DB name

        $this->conn = new mysqli($host, $user, $pass, $dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>


<?php
class JobModel {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $dbname = "job_portal_db";
    public $conn;

    public function __construct() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getAllJobs() {
        $sql = "SELECT * FROM jobs";
        return $this->conn->query($sql);
    }
}
?>
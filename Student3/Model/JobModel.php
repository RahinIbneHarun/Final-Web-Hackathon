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

    public function getCategories() {
        $sql = "SELECT * FROM categories";
        return $this->conn->query($sql);
    }

    public function getActiveJobs() {
        $sql = "SELECT * FROM jobs WHERE status = 'active' ORDER BY id DESC";
        return $this->conn->query($sql);
    }

    public function getCompanyNameByUserId($user_id) {
        $stmt = $this->conn->prepare("SELECT company_name FROM employer_profiles WHERE user_id = ?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        return $row ? $row['company_name'] : null;
    }

    public function searchJobs($term) {
        $like = "%" . $term . "%";
        $stmt = $this->conn->prepare("SELECT * FROM jobs WHERE (title LIKE ? OR location LIKE ?) AND status = 'active'");
        $stmt->bind_param('ss', $like, $like);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function saveJob($user_id, $job_id) {
        $stmt = $this->conn->prepare("INSERT INTO saved_jobs (user_id, job_id) VALUES (?, ?)");
        $stmt->bind_param('ii', $user_id, $job_id);
        return $stmt->execute();
    }

    public function removeSavedJob($user_id, $job_id) {
        $stmt = $this->conn->prepare("DELETE FROM saved_jobs WHERE job_id = ? AND user_id = ?");
        $stmt->bind_param('ii', $job_id, $user_id);
        return $stmt->execute();
    }

    public function applyJob($job_id, $seeker_id, $cover_letter, $resume_path) {
        $status = 'Submitted';
        $stmt = $this->conn->prepare("INSERT INTO applications (job_id, seeker_id, cover_letter, resume_path, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('iisss', $job_id, $seeker_id, $cover_letter, $resume_path, $status);
        return $stmt->execute();
    }

    public function getSavedJobsByUser($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM saved_jobs WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getJobById($job_id) {
        $stmt = $this->conn->prepare("SELECT * FROM jobs WHERE id = ?");
        $stmt->bind_param('i', $job_id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc();
    }

    public function isJobSaved($user_id, $job_id) {
        $stmt = $this->conn->prepare("SELECT id FROM saved_jobs WHERE job_id = ? AND user_id = ?");
        $stmt->bind_param('ii', $job_id, $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->num_rows > 0;
    }

    public function getApplicationStatus($job_id, $seeker_id) {
        $stmt = $this->conn->prepare("SELECT status FROM applications WHERE job_id = ? AND seeker_id = ?");
        $stmt->bind_param('ii', $job_id, $seeker_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        return $row ? $row['status'] : null;
    }

    public function updateUserProfile($user_id, $name, $email, $skills, $experience) {
        $stmt = $this->conn->prepare("UPDATE users SET name=?, email=?, skills=?, experience=? WHERE id=?");
        $stmt->bind_param('ssssi', $name, $email, $skills, $experience, $user_id);
        return $stmt->execute();
    }

    public function getFilteredJobs($category_id = '', $location = '', $job_type = '', $salary = '') {
        $sql = "SELECT * FROM jobs WHERE status = 'active'";

        if ($category_id !== '') {
            $sql .= " AND category_id = " . intval($category_id);
        }

        if ($location !== '') {
            $safe_location = $this->conn->real_escape_string($location);
            $sql .= " AND location LIKE '%" . $safe_location . "%'";
        }

        if ($job_type !== '') {
            $safe_job_type = $this->conn->real_escape_string($job_type);
            $sql .= " AND job_type = '" . $safe_job_type . "'";
        }

        if ($salary !== '') {
            $safe_salary = intval($salary);
            $sql .= " AND CAST(REPLACE(REPLACE(salary_range, ',', ''), ' BDT', '') AS UNSIGNED) >= " . $safe_salary;
        }

        $sql .= " ORDER BY id DESC";
        return $this->conn->query($sql);
    }

    public function getFirstUser() {
        $sql = "SELECT * FROM users LIMIT 1";
        $res = $this->conn->query($sql);
        return $res ? $res->fetch_assoc() : null;
    }

    public function getUserById($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    /**
     * Get all jobs applied by a specific seeker.
     *
     * @param int $user_id
     * @return mysqli_result|null
     */
    public function getAppliedJobsByUser($user_id) {
        $stmt = $this->conn->prepare(
            "SELECT j.*, a.status AS application_status, a.cover_letter, a.resume_path, a.created_at AS applied_at
             FROM applications a
             JOIN jobs j ON a.job_id = j.id
             WHERE a.seeker_id = ?
             ORDER BY a.id DESC"
        );
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        return $stmt->get_result() ?: null;
    }
}
?>

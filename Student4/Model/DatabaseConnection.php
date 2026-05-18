<?php
class DatabaseConnection{
    function openConnection(){
        $db_host = "localhost";
        $db_username = "root";
        $db_password = "";
        $db_name = "job_portal_db";

        $connection = new mysqli($db_host, $db_username, $db_password, $db_name);

        if($connection->connect_error){
            die("Could not connect to the database. Please try again. Original Error ".$connection->connect_error);
        }

        return $connection;
    }

    function getEmployerJobs($connection, $employer_id){
        $employer_id = (int)$employer_id;
        $sql = "SELECT id, title FROM jobs WHERE employer_id = ".$employer_id." ORDER BY id DESC";
        return $connection->query($sql);
    }

    function getApplicationSummary($connection, $employer_id){
        $employer_id = (int)$employer_id;
        $sql = "SELECT 
                    COUNT(applications.id) AS total_applications,
                    SUM(CASE WHEN applications.status = 'Submitted' THEN 1 ELSE 0 END) AS submitted_count,
                    SUM(CASE WHEN applications.status = 'Reviewed' THEN 1 ELSE 0 END) AS reviewed_count,
                    SUM(CASE WHEN applications.status = 'Shortlisted' THEN 1 ELSE 0 END) AS shortlisted_count,
                    SUM(CASE WHEN applications.status = 'Rejected' THEN 1 ELSE 0 END) AS rejected_count
                FROM applications
                INNER JOIN jobs ON jobs.id = applications.job_id
                WHERE jobs.employer_id = ".$employer_id;

        $result = $connection->query($sql);

        if($result && $result->num_rows == 1){
            return $result->fetch_assoc();
        }

        return [
            "total_applications" => 0,
            "submitted_count" => 0,
            "reviewed_count" => 0,
            "shortlisted_count" => 0,
            "rejected_count" => 0
        ];
    }

    function getEmployerApplications($connection, $employer_id, $job_id = "", $status = ""){
        $employer_id = (int)$employer_id;
        $sql = "SELECT applications.id, applications.job_id, applications.cover_letter, applications.resume_path, applications.status, applications.created_at,
                       jobs.title,
                       users.name AS seeker_name,
                       users.email AS seeker_email
                FROM applications
                INNER JOIN jobs ON jobs.id = applications.job_id
                INNER JOIN users ON users.id = applications.seeker_id
                WHERE jobs.employer_id = ".$employer_id;

        if($job_id != ""){
            $job_id = (int)$job_id;
            $sql .= " AND applications.job_id = ".$job_id;
        }

        if($status != ""){
            $sql .= " AND applications.status = '".$status."'";
        }

        $sql .= " ORDER BY applications.created_at DESC, applications.id DESC";

        return $connection->query($sql);
    }

    function getEmployerApplicationById($connection, $application_id, $employer_id){
        $application_id = (int)$application_id;
        $employer_id = (int)$employer_id;
        $sql = "SELECT applications.id, applications.job_id, applications.cover_letter, applications.resume_path, applications.status, applications.created_at,
                       jobs.title, jobs.location, jobs.job_type, jobs.salary_range, jobs.deadline,
                       users.name AS seeker_name,
                       users.email AS seeker_email
                FROM applications
                INNER JOIN jobs ON jobs.id = applications.job_id
                INNER JOIN users ON users.id = applications.seeker_id
                WHERE applications.id = ".$application_id." AND jobs.employer_id = ".$employer_id;

        return $connection->query($sql);
    }

    function updateApplicationStatus($connection, $application_id, $employer_id, $status){
        $application_id = (int)$application_id;
        $employer_id = (int)$employer_id;
        $sql = "UPDATE applications
                INNER JOIN jobs ON jobs.id = applications.job_id
                SET applications.status = '".$status."'
                WHERE applications.id = ".$application_id." AND jobs.employer_id = ".$employer_id;

        return $connection->query($sql);
    }
}
?>

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

    function getCategories($connection){
        $sql = "SELECT categories.id, categories.name, COUNT(jobs.id) AS job_count
                FROM categories
                LEFT JOIN jobs ON jobs.category_id = categories.id
                GROUP BY categories.id, categories.name
                ORDER BY categories.name ASC";

        return $connection->query($sql);
    }

    function getCategoryById($connection, $category_id){
        $category_id = (int)$category_id;
        $sql = "SELECT * FROM categories WHERE id = ".$category_id;

        return $connection->query($sql);
    }

    function createCategory($connection, $name){
        $sql = "INSERT INTO categories (name) VALUES ('".$name."')";

        return $connection->query($sql);
    }

    function updateCategory($connection, $category_id, $name){
        $category_id = (int)$category_id;
        $sql = "UPDATE categories SET name = '".$name."' WHERE id = ".$category_id;

        return $connection->query($sql);
    }

    function categoryHasJobs($connection, $category_id){
        $category_id = (int)$category_id;
        $sql = "SELECT COUNT(*) AS total_jobs FROM jobs WHERE category_id = ".$category_id;
        $result = $connection->query($sql);

        if($result && $result->num_rows > 0){
            $row = $result->fetch_assoc();
            return $row["total_jobs"] > 0;
        }

        return false;
    }

    function deleteCategory($connection, $category_id){
        $category_id = (int)$category_id;
        $sql = "DELETE FROM categories WHERE id = ".$category_id;

        return $connection->query($sql);
    }

    function getEmployerJobs($connection, $employer_id){
        $employer_id = (int)$employer_id;
        $sql = "SELECT jobs.id, jobs.title, categories.name AS category_name, jobs.deadline, jobs.status,
                       COUNT(applications.id) AS application_count
                FROM jobs
                INNER JOIN categories ON categories.id = jobs.category_id
                LEFT JOIN applications ON applications.job_id = jobs.id
                WHERE jobs.employer_id = ".$employer_id."
                GROUP BY jobs.id, jobs.title, categories.name, jobs.deadline, jobs.status
                ORDER BY jobs.id DESC";

        return $connection->query($sql);
    }

    function getJobByIdForEmployer($connection, $job_id, $employer_id){
        $job_id = (int)$job_id;
        $employer_id = (int)$employer_id;
        $sql = "SELECT * FROM jobs WHERE id = ".$job_id." AND employer_id = ".$employer_id;

        return $connection->query($sql);
    }

    function createJob($connection, $employer_id, $category_id, $title, $description, $requirements, $salary_range, $location, $job_type, $deadline, $status){
        $employer_id = (int)$employer_id;
        $category_id = (int)$category_id;

        $sql = "INSERT INTO jobs (employer_id, category_id, title, description, requirements, salary_range, location, job_type, deadline, status)
                VALUES ('".$employer_id."', '".$category_id."', '".$title."', '".$description."', '".$requirements."', '".$salary_range."', '".$location."', '".$job_type."', '".$deadline."', '".$status."')";

        return $connection->query($sql);
    }

    function updateJob($connection, $job_id, $employer_id, $category_id, $title, $description, $requirements, $salary_range, $location, $job_type, $deadline){
        $job_id = (int)$job_id;
        $employer_id = (int)$employer_id;
        $category_id = (int)$category_id;

        $sql = "UPDATE jobs
                SET category_id = '".$category_id."',
                    title = '".$title."',
                    description = '".$description."',
                    requirements = '".$requirements."',
                    salary_range = '".$salary_range."',
                    location = '".$location."',
                    job_type = '".$job_type."',
                    deadline = '".$deadline."'
                WHERE id = ".$job_id." AND employer_id = ".$employer_id;

        return $connection->query($sql);
    }

    function deleteJob($connection, $job_id, $employer_id){
        $job_id = (int)$job_id;
        $employer_id = (int)$employer_id;
        $sql = "DELETE FROM jobs WHERE id = ".$job_id." AND employer_id = ".$employer_id;

        return $connection->query($sql);
    }

    function toggleJobStatus($connection, $job_id, $employer_id){
        $job_id = (int)$job_id;
        $employer_id = (int)$employer_id;

        $sql = "SELECT status FROM jobs WHERE id = ".$job_id." AND employer_id = ".$employer_id;
        $result = $connection->query($sql);

        if(!$result || $result->num_rows != 1){
            return false;
        }

        $row = $result->fetch_assoc();
        $new_status = "active";

        if($row["status"] == "active"){
            $new_status = "closed";
        }

        $update_sql = "UPDATE jobs SET status = '".$new_status."' WHERE id = ".$job_id." AND employer_id = ".$employer_id;
        $update_result = $connection->query($update_sql);

        if($update_result){
            return $new_status;
        }

        return false;
    }
}
?>

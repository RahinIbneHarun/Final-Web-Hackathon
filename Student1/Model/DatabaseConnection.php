<?php
class DatabaseConnection {
    function openConnection(){
        $db_host = "localhost";
        $db_username = "root";
        $db_password = "";
        $db_name = "job_portal_db";
        $connection = new mysqli($db_host, $db_username, $db_password, $db_name);

        if($connection->connect_error){
            die("Could not connect to the database. Please try again with different parameters. Original Error ".$connection->connect_error);
        }
        return $connection;
    }

    function CreateUserWithPrepareStmt($connection, $tableName, $name, $email, $password_hash, $role, $file_path){
        $sql = "INSERT INTO $tableName (name, email, password_hash, role, file_path) VALUES (?, ?, ?, ?, ?)";
        $statement = $connection->prepare($sql);
        $statement->bind_param("sssss", $name, $email, $password_hash, $role, $file_path);

        $result = $statement->execute();
        return $result;
    }

    function GetUserByEmailWithPrepareStmt($connection, $tableName, $email){
        $sql = "SELECT * FROM $tableName WHERE email = ?";
        $statement = $connection->prepare($sql);
        $statement->bind_param("s", $email);
        $statement->execute();

        $result = $statement->get_result();
        return $result;
    }

    function GetUserByIdWithPrepareStmt($connection, $tableName, $user_id){
        $sql = "SELECT * FROM $tableName WHERE id = ?";
        $statement = $connection->prepare($sql);
        $statement->bind_param("i", $user_id);
        $statement->execute();

        $result = $statement->get_result();
        return $result;
    }

    function UpdateUserPasswordWithPrepareStmt($connection, $tableName, $user_id, $password_hash){
        $sql = "UPDATE $tableName SET password_hash = ? WHERE id = ?";
        $statement = $connection->prepare($sql);
        $statement->bind_param("si", $password_hash, $user_id);

        $result = $statement->execute();
        return $result;
    }

    function UpdateUserFileWithPrepareStmt($connection, $tableName, $user_id, $file_path){
        $sql = "UPDATE $tableName SET file_path = ? WHERE id = ?";
        $statement = $connection->prepare($sql);
        $statement->bind_param("si", $file_path, $user_id);

        $result = $statement->execute();
        return $result;
    }

    function GetProfileByUserIdWithPrepareStmt($connection, $tableName, $user_id){
        $sql = "SELECT * FROM $tableName WHERE user_id = ?";
        $statement = $connection->prepare($sql);
        $statement->bind_param("i", $user_id);
        $statement->execute();

        $result = $statement->get_result();
        return $result;
    }

    function CreateEmployerProfileWithPrepareStmt($connection, $tableName, $user_id, $company_name, $industry, $description, $website){
        $sql = "INSERT INTO $tableName (user_id, company_name, industry, description, website) VALUES (?, ?, ?, ?, ?)";
        $statement = $connection->prepare($sql);
        $statement->bind_param("issss", $user_id, $company_name, $industry, $description, $website);

        $result = $statement->execute();
        return $result;
    }

    function UpdateEmployerProfileWithPrepareStmt($connection, $tableName, $user_id, $company_name, $industry, $description, $website){
        $sql = "UPDATE $tableName SET company_name = ?, industry = ?, description = ?, website = ? WHERE user_id = ?";
        $statement = $connection->prepare($sql);
        $statement->bind_param("ssssi", $company_name, $industry, $description, $website, $user_id);

        $result = $statement->execute();
        return $result;
    }

    function CreateSeekerProfileWithPrepareStmt($connection, $tableName, $user_id, $headline, $skills, $years_experience){
        $sql = "INSERT INTO $tableName (user_id, headline, skills, years_experience) VALUES (?, ?, ?, ?)";
        $statement = $connection->prepare($sql);
        $statement->bind_param("issi", $user_id, $headline, $skills, $years_experience);

        $result = $statement->execute();
        return $result;
    }

    function UpdateSeekerProfileWithPrepareStmt($connection, $tableName, $user_id, $headline, $skills, $years_experience){
        $sql = "UPDATE $tableName SET headline = ?, skills = ?, years_experience = ? WHERE user_id = ?";
        $statement = $connection->prepare($sql);
        $statement->bind_param("ssii", $headline, $skills, $years_experience, $user_id);

        $result = $statement->execute();
        return $result;
    }
}
?>

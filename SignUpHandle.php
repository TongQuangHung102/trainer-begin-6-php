<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = $_POST["fullname"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $password = $_POST["password"];
    $confirmpassword = $_POST["confirmpassword"];

    $roleId = 2;

    $avatar_path = null;

    if (isset($_FILES["avatar"]) && $_FILES["avatar"]["error"] == UPLOAD_ERR_OK) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["avatar"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed_types = array("jpg", "jpeg", "png", "gif");
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
                $avatar_path = $target_file;
            } else {
                header("Location: ./SignUp.php?error=avataruploadfailed");
                die();
            }
        } else {
            header("Location: ./SignUp.php?error=invalidavatartype");
            die();
        }
    }

    $errors = [];

    if (empty($fullname) || empty($username) || empty($email) || empty($phone) || empty($password) || empty($confirmpassword)) {
        $errors[] = "emptyfields";
    }

    if ($password !== $confirmpassword) {
        $errors[] = "passwordmismatch";
    }

    try {
        require_once "./includes/dbh.inc.php";

        $query_username = "SELECT UserName FROM users WHERE UserName = :username;";
        $stmt_username = $pdo->prepare($query_username);
        $stmt_username->bindParam(":username", $username);
        $stmt_username->execute();
        if ($stmt_username->fetch(PDO::FETCH_ASSOC)) {
            $errors[] = "usernameexists";
        }

        $query_email = "SELECT Email FROM users WHERE Email = :email;";
        $stmt_email = $pdo->prepare($query_email);
        $stmt_email->bindParam(":email", $email);
        $stmt_email->execute();
        if ($stmt_email->fetch(PDO::FETCH_ASSOC)) {
            $errors[] = "emailexists";
        }

        $query_phone = "SELECT Phone FROM users WHERE Phone = :phone;";
        $stmt_phone = $pdo->prepare($query_phone);
        $stmt_phone->bindParam(":phone", $phone);
        $stmt_phone->execute();
        if ($stmt_phone->fetch(PDO::FETCH_ASSOC)) {
            $errors[] = "phoneexists";
        }
    } catch (PDOException $e) {

        error_log("Database connection error: " . $e->getMessage());
        header("Location: ./SignUp.php?error=dbconnection");
        die();
    }

    if (!empty($errors)) {

        header("Location: ./SignUp.php?error=" . implode(",", $errors));
        die();
    }

    $hashedPwd = password_hash($password, PASSWORD_DEFAULT);

    try {
        $query = "INSERT INTO users (FullName, UserName, Password, Email, Phone, Avatar, RoleId) VALUES (:fullname, :username, :hashedPassword, :email, :phone, :avatar, :roleId);";
        $stmt = $pdo->prepare($query);

        $stmt->bindParam(":fullname", $fullname);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":hashedPassword", $hashedPwd);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":avatar", $avatar_path);
        $stmt->bindParam(":roleId", $roleId);

        $stmt->execute();

        $pdo = null;
        $stmt = null;

        header("Location: ./SignUp.php?signup=success");
        die();
    } catch (PDOException $e) {

        error_log("Database query error: " . $e->getMessage());
        header("Location: ./SignUp.php?error=dbqueryfailed");
        die();
    }
} else {
    header("Location: ./SignUp.php");
    die();
}

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = $_POST["fullname"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $password = $_POST["password"];
    $confirmpassword = $_POST["confirmpassword"];
    $referrerEmail = $_POST['referrer'] ?? null;
    $roleId = 2;
    // Phần xử lý file avatar
    $avatar_path = null;

    if (isset($_FILES["avatar"]) && $_FILES["avatar"]["error"] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
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
    // Phần xử lý lỗi để gửi về SignUp.
    $errors = [];
    if ($password !== $confirmpassword) {
        $errors[] = "passwordmismatch";
    }
    if (!empty($phone)) {
        $vietnam_phone_regex = '/^(0|\+84)(3|5|7|8|9)[0-9]{8}$/';
        if (!preg_match($vietnam_phone_regex, $phone)) {
            $errors[] = "invalidphoneformat";
        }
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
    // Phần xử lý người giới thiệu
    $referrerId = null;
    if ($referrerEmail) {
        try {
            $query_referrer = "SELECT UserId FROM users WHERE Email = :referrerEmail;";
            $stmt_referrer = $pdo->prepare($query_referrer);
            $stmt_referrer->bindParam(":referrerEmail", $referrerEmail);
            $stmt_referrer->execute();
            $referrer = $stmt_referrer->fetch(PDO::FETCH_ASSOC);
            if ($referrer) {
                $referrerId = $referrer['UserId'];
            } else {
                $errors[] = "invalidreferrer";
            }
        } catch (PDOException $e) {
            error_log("Referrer query error: " . $e->getMessage());
        }
    }
    if (!empty($errors)) {

        header("Location: ./SignUp.php?error=" . implode(",", $errors));
        die();
    }
    // Mã hóa mật khẩu.
    $hashedPwd = password_hash($password, PASSWORD_DEFAULT);

    // Phần Insert dữ liệu vào database
    try {
        $query = "INSERT INTO users (FullName, UserName, Password, Email, Phone, Avatar, RoleId, ReferrerId) VALUES (:fullname, :username, :hashedPassword, :email, :phone, :avatar, :roleId, :referrerId);";
        $stmt = $pdo->prepare($query);

        $stmt->bindParam(":fullname", $fullname);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":hashedPassword", $hashedPwd);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":avatar", $avatar_path);
        $stmt->bindParam(":roleId", $roleId);
        $stmt->bindParam(":referrerId", $referrerId, PDO::PARAM_INT);
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

<?php
require_once 'includes/dbh.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $userId = filter_input(INPUT_POST, 'userId', FILTER_VALIDATE_INT);

    $errors = [];


    if ($userId === false || $userId <= 0) {
        $errors[] = 'ID người dùng không hợp lệ.';
    }


    $fullName = filter_input(INPUT_POST, 'fullName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $userName = filter_input(INPUT_POST, 'userName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $birthday = filter_input(INPUT_POST, 'birthday', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $roleId = filter_input(INPUT_POST, 'roleId', FILTER_VALIDATE_INT);
    $referrerEmail = filter_input(INPUT_POST, 'referrerEmail', FILTER_SANITIZE_EMAIL);


    if (empty($fullName)) {
        $errors[] = 'fullname_empty';
    }
    if (empty($userName)) {
        $errors[] = 'username_empty';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'invalidemail';
    }
    if ($roleId === false || $roleId <= 0) {
        $errors[] = 'invalidrole';
    }

    if (!empty($birthday) && $birthday >= date("Y-m-d")) {
        $errors[] = 'invalidbirthday';
    }


    if (!empty($phone)) {
        $vietnam_phone_regex = '/^(0|\+84)(3|5|7|8|9)[0-9]{8}$/';
        if (!preg_match($vietnam_phone_regex, $phone)) {
            $errors[] = "invalidphoneformat";
        }
    }

    $avatar_path = null;
    $currentAvatarPath = null;

    if ($userId > 0) {
        try {
            $stmtCurrentAvatar = $pdo->prepare("SELECT Avatar FROM users WHERE UserId = :userId");
            $stmtCurrentAvatar->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmtCurrentAvatar->execute();
            $currentAvatarPath = $stmtCurrentAvatar->fetchColumn();
        } catch (PDOException $e) {

            error_log("Database error fetching current avatar for user ID " . $userId . ": " . $e->getMessage());
        }
    }


    if (isset($_FILES["avatarupdate"]) && $_FILES["avatarupdate"]["error"] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $file_extension = strtolower(pathinfo($_FILES["avatarupdate"]["name"], PATHINFO_EXTENSION));
        $file_name = uniqid('avatar_') . '.' . $file_extension;
        $target_file = $target_dir . $file_name;


        $allowed_types = array("jpg", "jpeg", "png", "gif");


        if (in_array($file_extension, $allowed_types)) {

            if (move_uploaded_file($_FILES["avatarupdate"]["tmp_name"], $target_file)) {
                $avatar_path = $target_file;


                if ($currentAvatarPath && file_exists($currentAvatarPath) && basename($currentAvatarPath) !== 'default_avatar.png') {
                    if (!unlink($currentAvatarPath)) {
                        error_log("Failed to delete old avatar: " . $currentAvatarPath);
                    }
                }
            } else {
                $errors[] = "avataruploadfailed";
            }
        } else {
            $errors[] = "invalidavatartype";
        }
    }


    try {

        $query_username = "SELECT UserId FROM users WHERE UserName = :username AND UserId != :userId;";
        $stmt_username = $pdo->prepare($query_username);
        $stmt_username->bindParam(":username", $userName);
        $stmt_username->bindParam(":userId", $userId, PDO::PARAM_INT);
        $stmt_username->execute();
        if ($stmt_username->fetch(PDO::FETCH_ASSOC)) {
            $errors[] = "usernameexists";
        }


        $query_email = "SELECT UserId FROM users WHERE Email = :email AND UserId != :userId;";
        $stmt_email = $pdo->prepare($query_email);
        $stmt_email->bindParam(":email", $email);
        $stmt_email->bindParam(":userId", $userId, PDO::PARAM_INT);
        $stmt_email->execute();
        if ($stmt_email->fetch(PDO::FETCH_ASSOC)) {
            $errors[] = "emailexists";
        }


        if (!empty($phone)) {
            $query_phone = "SELECT UserId FROM users WHERE Phone = :phone AND UserId != :userId;";
            $stmt_phone = $pdo->prepare($query_phone);
            $stmt_phone->bindParam(":phone", $phone);
            $stmt_phone->bindParam(":userId", $userId, PDO::PARAM_INT);
            $stmt_phone->execute();
            if ($stmt_phone->fetch(PDO::FETCH_ASSOC)) {
                $errors[] = "phoneexists";
            }
        }


        $actualReferrerId = null;
        if (!empty($referrerEmail)) {
            $stmtReferrer = $pdo->prepare("SELECT UserId FROM users WHERE Email = :email");
            $stmtReferrer->bindParam(':email', $referrerEmail);
            $stmtReferrer->execute();
            $referrerUser = $stmtReferrer->fetch(PDO::FETCH_ASSOC);
            if ($referrerUser) {
                $actualReferrerId = $referrerUser['UserId'];
            } else {
                $errors[] = 'invalidreferrer';
            }
        }


        if (!empty($errors)) {

            if (in_array('ID người dùng không hợp lệ.', $errors)) {
                header("Location: index.php?status=error&msg=" . urlencode(implode(",", $errors)));
            } else {
                header("Location: UserDetail.php?id=" . $userId . "&status=error&msg=" . urlencode(implode(",", $errors)));
            }
            exit();
        }


        $updateSql = "UPDATE users SET
                            FullName = :fullName,
                            UserName = :userName,
                            Email = :email,
                            Phone = :phone,
                            Birthday = :birthday,
                            Gender = :gender,
                            Description = :description,
                            RoleId = :roleId,
                            ReferrerId = :referrerId";


        if ($avatar_path !== null) {
            $updateSql .= ", Avatar = :avatar_new_path";
        }

        $updateSql .= " WHERE UserId = :userId";

        $updateStmt = $pdo->prepare($updateSql);


        $updateStmt->bindParam(':fullName', $fullName);
        $updateStmt->bindParam(':userName', $userName);
        $updateStmt->bindParam(':email', $email);
        $updateStmt->bindParam(':phone', $phone);
        $updateStmt->bindParam(':birthday', $birthday);
        $updateStmt->bindParam(':gender', $gender);
        $updateStmt->bindParam(':description', $description);
        $updateStmt->bindParam(':roleId', $roleId, PDO::PARAM_INT);
        $updateStmt->bindParam(':referrerId', $actualReferrerId, PDO::PARAM_INT);
        $updateStmt->bindParam(':userId', $userId, PDO::PARAM_INT);


        if ($avatar_path !== null) {
            $updateStmt->bindParam(':avatar_new_path', $avatar_path);
        }


        if ($updateStmt->execute()) {

            header("Location: UserDetail.php?id=" . $userId . "&status=success");
            exit();
        } else {

            $errorInfo = $updateStmt->errorInfo();
            error_log("PDO Statement Execute Error: " . $errorInfo[2]);

            header("Location: UserDetail.php?id=" . $userId . "&status=error&msg=" . urlencode('dbqueryfailed_update'));
            exit();
        }
    } catch (PDOException $e) {

        error_log("Database error during user update: " . $e->getMessage());
        header("Location: UserDetail.php?id=" . $userId . "&status=error&msg=" . urlencode('dbconnection'));
        exit();
    }
} else {

    header("Location: index.php?status=error&msg=" . urlencode('invalidrequest'));
    exit();
}

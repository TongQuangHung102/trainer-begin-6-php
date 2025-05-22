<?php
require_once 'includes/dbh.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $userId = filter_input(INPUT_POST, 'userId', FILTER_VALIDATE_INT);

    if ($userId === false || $userId <= 0) {
        header("Location: UserDetail.php?id=" . $userId . "&status=error&msg=" . urlencode('ID người dùng không hợp lệ.'));
        exit();
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

    $errors = [];


    if (empty($fullName)) {
        $errors[] = 'Tên đầy đủ không được để trống.';
    }
    if (empty($userName)) {
        $errors[] = 'Tên đăng nhập không được để trống.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ.';
    }
    if ($roleId === false || $roleId <= 0) {
        $errors[] = 'Vai trò không hợp lệ.';
    }

    if (!empty($errors)) {

        $errorMsg = urlencode(implode('<br>', $errors));
        header("Location: UserDetail.php?id=" . $userId . "&status=error&msg=" . $errorMsg);
        exit();
    }

    try {

        $actualReferrerId = null;
        if (!empty($referrerEmail)) {
            $stmtReferrer = $pdo->prepare("SELECT UserId FROM users WHERE Email = :email");
            $stmtReferrer->bindParam(':email', $referrerEmail);
            $stmtReferrer->execute();
            $referrerUser = $stmtReferrer->fetch(PDO::FETCH_ASSOC);
            if ($referrerUser) {
                $actualReferrerId = $referrerUser['UserId'];
            } else {

                $errors[] = 'Email người giới thiệu không tồn tại.';
            }
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
                        ReferrerId = :referrerId
                      WHERE UserId = :userId";

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

        if ($updateStmt->execute()) {

            header("Location: UserDetail.php?id=" . $userId . "&status=success");
            exit();
        } else {
            header("Location: UserDetail.php?id=" . $userId . "&status=error&msg=" . urlencode('Cập nhật thất bại, vui lòng thử lại.'));
            exit();
        }
    } catch (PDOException $e) {
        error_log("Database error during user update: " . $e->getMessage());
        header("Location: UserDetail.php?id=" . $userId . "&status=error&msg=" . urlencode('Lỗi hệ thống, vui lòng thử lại sau.'));
        exit();
    }
} else {

    header("Location: index.php?status=error&msg=" . urlencode('Yêu cầu không hợp lệ.'));
    exit();
}

<?php
session_start();
require_once "./includes/dbh.inc.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usernameoremail = trim($_POST['usernameoremail'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($usernameoremail) || empty($password)) {
        $_SESSION['error_message'] = 'Vui lòng nhập đầy đủ tên đăng nhập hoặc email và mật khẩu.';
        header('Location: Login.php');
        exit();
    }

    try {

        $stmt = $pdo->prepare("SELECT UserId, UserName, Email, Password, RoleId FROM users WHERE UserName = :usernameoremail OR Email = :usernameoremail");
        $stmt->execute([':usernameoremail' => $usernameoremail]);
        $user = $stmt->fetch();

        if ($user) {

            if (password_verify($password, $user['Password'])) {

                $_SESSION['user_id'] = $user['UserId'];
                $_SESSION['username'] = $user['UserName'];
                $_SESSION['user_email'] = $user['Email'];
                $_SESSION['user_role_id'] = $user['RoleId'];

                $_SESSION['success_message'] = 'Đăng nhập thành công!';


                header('Location: index.php');
                exit();
            } else {

                $_SESSION['error_message'] = 'Sai mật khẩu. Vui lòng thử lại.';

                $_SESSION['old_usernameoremail'] = $usernameoremail;
                header('Location: Login.php');
                exit();
            }
        } else {

            $_SESSION['error_message'] = 'Tên đăng nhập hoặc email không tồn tại.';

            $_SESSION['old_usernameoremail'] = $usernameoremail;
            header('Location: Login.php');
            exit();
        }
    } catch (PDOException $e) {

        error_log("Login error: " . $e->getMessage());
        $_SESSION['error_message'] = 'Đã xảy ra lỗi hệ thống. Vui lòng thử lại sau.';
        header('Location: Login.php');
        exit();
    }
} else {

    header('Location: Login.php');
    exit();
}

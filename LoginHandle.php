<?php
session_start();
// Thiết lập múi giờ mặc định => 'Asia/Ho_Chi_Minh' (Múi giờ chuẩn của Việt Nam)
date_default_timezone_set('Asia/Ho_Chi_Minh');
require_once "includes/dbh.inc.php";
//  Số lần đăng nhập sai tối đa trước khi tài khoản bị khóa.
const MAX_FAILED_ATTEMPTS = 3;
// Thời gian tài khoản bị khóa sau khi đạt số lần đăng nhập sai tối đa
const LOCKOUT_DURATION_SECONDS = 60;
// Thời gian để reset lại số lần đăng nhập sai nếu lần thử sai gần nhất đã quá khoảng thời gian này
const RESET_ATTEMPTS_TIME_SECONDS = 60;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usernameoremail = trim($_POST['usernameoremail'] ?? '');
    $password = $_POST['password'] ?? '';
    $_SESSION['old_usernameoremail'] = $usernameoremail;
    if (empty($usernameoremail) || empty($password)) {
        $_SESSION['error_message'] = 'Vui lòng nhập đầy đủ tên đăng nhập hoặc email và mật khẩu.';
        header('Location: Login.php');
        exit();
    }

    try {

        $stmt = $pdo->prepare("SELECT UserId, UserName, Email, Password, RoleId, failed_login_attempts, last_failed_login, lockout_until FROM users WHERE UserName = :usernameoremail OR Email = :usernameoremail");
        $stmt->execute([':usernameoremail' => $usernameoremail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);


        if (!$user) {

            $_SESSION['error_message'] = 'Tên đăng nhập(Email) hoặc mật khẩu không đúng.';
            header('Location: Login.php');
            exit();
        }

        $userId = $user['UserId'];
        $currentFailedAttempts = (int)$user['failed_login_attempts'];
        $lastFailedLoginTimestamp = $user['last_failed_login'] ? strtotime($user['last_failed_login']) : 0;
        $lockoutUntilTimestamp = $user['lockout_until'] ? strtotime($user['lockout_until']) : 0;
        // Kiểm tra xem thời gian khóa tài khoản có còn hiệu lực
        if ($lockoutUntilTimestamp > time()) {

            $remainingTimeSeconds = $lockoutUntilTimestamp - time();
            $remainingMinutes = ceil($remainingTimeSeconds / 60);
            $_SESSION['error_message'] = 'Tài khoản đã bị khóa. Vui lòng thử lại sau ' . $remainingMinutes . ' phút.';
            header('Location: Login.php');
            exit();
        }
        // Xác thực mật khẩu mã hóa
        if (password_verify($password, $user['Password'])) {

            $updateQuery = "UPDATE users SET failed_login_attempts = 0, last_failed_login = NULL, lockout_until = NULL WHERE UserId = :userId";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->bindParam(":userId", $userId, PDO::PARAM_INT);
            $updateStmt->execute();


            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $user['UserName'];
            $_SESSION['user_email'] = $user['Email'];
            $_SESSION['user_role_id'] = $user['RoleId'];

            $_SESSION['success_message'] = 'Đăng nhập thành công!';
            header('Location: index.php');
            exit();
        } else {
            //Ví dụ: 2025-05-26 10:05:34
            $now = date('Y-m-d H:i:s');
            // Kiểm tra xem đây có phải là lần đăng nhập sai đầu tiên của người dùng đó hay không.
            if ($lastFailedLoginTimestamp === 0 || (time() - $lastFailedLoginTimestamp) > RESET_ATTEMPTS_TIME_SECONDS) {
                $newFailedAttempts = 1;
            } else {
                $newFailedAttempts = $currentFailedAttempts + 1;
            }

            $updateQuery = "UPDATE users SET failed_login_attempts = :attempts, last_failed_login = :now";
            $updateParams = [':attempts' => $newFailedAttempts, ':now' => $now];

            //Kiểm tra xem số lần thử sai mới có đạt đến hoặc vượt quá số lần tối đa cho phép hay không.
            if ($newFailedAttempts >= MAX_FAILED_ATTEMPTS) {
                
                $lockoutTime = date('Y-m-d H:i:s', time() + LOCKOUT_DURATION_SECONDS);
                $updateQuery .= ", lockout_until = :lockout_time";
                $updateParams[':lockout_time'] = $lockoutTime;


                $_SESSION['error_message'] = 'Bạn đã nhập sai mật khẩu ' . MAX_FAILED_ATTEMPTS . ' lần. Tài khoản đã bị khóa trong ' . (LOCKOUT_DURATION_SECONDS / 60) . ' phút.';
            } else {

                $_SESSION['error_message'] = 'Sai mật khẩu. Bạn còn ' . (MAX_FAILED_ATTEMPTS - $newFailedAttempts) . ' lần thử trong vòng 1 phút.';
            }

            $updateQuery .= " WHERE UserId = :userId";
            $updateParams[':userId'] = $userId;

            $updateStmt = $pdo->prepare($updateQuery);
            foreach ($updateParams as $key => $value) {
                $updateStmt->bindValue($key, $value);
            }
            $updateStmt->execute();
            header('Location: Login.php');
            exit();
        }
    } catch (PDOException $e) {
        error_log("Login PDO error: " . $e->getMessage());
        $_SESSION['error_message'] = 'Đã xảy ra lỗi hệ thống. Vui lòng thử lại sau.';
        header('Location: Login.php');
        exit();
    }
} else {

    header('Location: Login.php');
    exit();
}

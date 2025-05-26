<?php
session_start();
// Xóa sạch tất cả các biến đã được đăng ký trong session hiện tại.
$_SESSION = array();
// kiểm tra xem PHP có đang được cấu hình để sử dụng cookie để quản lý ID phiên hay không.
if (ini_get("session.use_cookies")) {
    // Lấy về các tham số cấu hình hiện tại của session cookie
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"], // chỉ gửi qua HTTPS
        $params["httponly"] // chỉ truy cập qua HTTP, không qua JavaScript
    );
}
// Chính thức hủy bỏ tất cả dữ liệu session đã được lưu trữ trên máy chủ.
session_destroy();
header("Location: index.php");
exit();

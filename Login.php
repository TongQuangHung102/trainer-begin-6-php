<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/reset.css">
    <link rel="stylesheet" href="./css/Login.module.css">
    <title>Đăng nhập</title>
</head>

<body>
    <h5 class="page-title">Đăng nhập</h5>
    <!-- Hiển thị message khi đăng nhập (Thành công, Thất bại) -->
    <?php
    session_start();
    //isset() hàm kiểm tra biến có tồn tại và không phải là NULL hay không.
    if (isset($_SESSION['error_message'])) {
        echo '<p class="message error-message">' . htmlspecialchars($_SESSION['error_message']) . '</p>';
        unset($_SESSION['error_message']);
    }


    if (isset($_SESSION['success_message'])) {
        echo '<p class="message success-message">' . htmlspecialchars($_SESSION['success_message']) . '</p>';
        unset($_SESSION['success_message']);
    }
    ?>  
    <!-- enctype="multipart/form-data" => Có input là file (chia dữ liệu của form thành nhiều phần (parts) riêng biệt. 
    Mỗi phần đại diện cho một trường input trong form.) -->
    <form action="LoginHandle.php" method="POST" enctype="multipart/form-data" class="login-form">
        <div class="form-control">
            <label class="form-label">Tên đăng nhập (Email)</label>
            <input
                type="text"
                name="usernameoremail"
                placeholder="Nhập tên đăng nhập hoặc email"
                required
                class="form-input"
                value="<?php
                        echo htmlspecialchars($_SESSION['old_usernameoremail'] ?? '');
                        unset($_SESSION['old_usernameoremail']);
                        ?>">
        </div>
        <div class="form-control">
            <label class="form-label" for="password">Mật khẩu</label>
            <div class="password-input-wrapper">
                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Nhập mật khẩu"
                    required
                    class="form-input">
                <span class="password-toggle" id="togglePassword">
                    Hiện </span>
            </div>
        </div>
        <button type="submit" class="submit-button">
            Đăng nhập
        </button>
        <div class="auth-links">
            <a href="SignUp.php" class="register-link">Bạn chưa có tài khoản? Đăng ký</a>
        </div>
    </form>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                if (type === 'password') {
                    togglePassword.textContent = 'Hiện';
                } else {
                    togglePassword.textContent = 'Ẩn';
                }
            });
        }
    });
</script>

</html>
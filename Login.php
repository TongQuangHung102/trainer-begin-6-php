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
    <form action="SignUpHandle.php" method="POST" enctype="multipart/form-data" class="login-form">
        <div class="form-control">
            <label class="form-label">Tên đăng nhập (Email)</label>
            <input
                type="text"
                name="username"
                placeholder="Nhập tên đăng nhập hoặc email"
                required
                class="form-input">
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
<!-- Phần xử lý Hiện, Ẩn của Mật khẩu-->
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
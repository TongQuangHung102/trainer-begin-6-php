<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/reset.css">
    <link rel="stylesheet" href="./css/SignUp.module.css">
    <title>Đăng ký tài khoản</title>
</head>

<body>
    <h5>Đăng ký tài khoản</h5>
    <!-- Phần xử lý lỗi từ SignUpHandle -> SignUp -->
    <?php
    if (isset($_GET["signup"]) && $_GET["signup"] == "success") {
        echo '<div class="message success">Đăng ký tài khoản thành công!</div>';
    } elseif (isset($_GET["error"])) {

        $errors = explode(",", $_GET["error"]);
        $firstError = $errors[0];

        echo '<div class="message error">';
        echo '<ul>';
        switch ($firstError) {
            case 'passwordmismatch':
                echo '<li>Mật khẩu xác nhận không khớp.</li>';
                break;
            case 'usernameexists':
                echo '<li>Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.</li>';
                break;
            case 'emailexists':
                echo '<li>Email đã được sử dụng. Vui lòng sử dụng email khác.</li>';
                break;
            case 'phoneexists':
                echo '<li>Số điện thoại đã được sử dụng. Vui lòng sử dụng số khác.</li>';
                break;
            case 'avataruploadfailed':
                echo '<li>Tải ảnh đại diện thất bại. Vui lòng thử lại.</li>';
                break;
            case 'invalidavatartype':
                echo '<li>Định dạng ảnh đại diện không hợp lệ (chỉ chấp nhận JPG, JPEG, PNG, GIF).</li>';
                break;
            case 'dbconnection':
                echo '<li>Lỗi kết nối cơ sở dữ liệu. Vui lòng thử lại sau.</li>';
                break;
            case 'dbqueryfailed':
                echo '<li>Đã xảy ra lỗi khi đăng ký. Vui lòng thử lại.</li>';
                break;
            case 'invalidreferrer':
                echo '<li>Email người giới thiệu không tồn tại trong hệ thống.</li>';
                break;
            case 'invalidphoneformat':
                echo '<li>Định dạng số điện thoại không hợp lệ. Vui lòng thử lại.</li>';
                break;
            default:
                echo '<li>Đã xảy ra lỗi không xác định.</li>';
                break;
        }
        echo '</ul>';
        echo '</div>';
    }
    ?>
    <!-- Form hiển thị cho người dùng -->
    <form action="SignUpHandle.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label class="form-label">Họ tên</label>
            <input
                type="text"
                name="fullname"
                placeholder="Nhập họ tên đầy đủ"
                required
                class="form-input">
        </div>
        <div class="form-group">
            <label class="form-label">Tên đăng nhập</label>
            <input
                type="text"
                name="username"
                placeholder="Nhập tên đăng nhập"
                required
                class="form-input">
        </div>
        <div class="form-group">
            <label class="form-label">Email</label>
            <input
                type="email"
                name="email"
                placeholder="Nhập email"
                required
                class="form-input">
        </div>
        <div class="form-group">
            <label class="form-label">Số điện thoại</label>
            <input
                type="text"
                name="phone"
                placeholder="Nhập số điện thoại"
                required
                class="form-input">
        </div>
        <!-- Phần Avatar của người dùng -->
        <div class="form-group">
            <label class="form-label" for="avatar">Ảnh đại diện</label>
            <input
                type="file"
                id="avatar"
                name="avatar"
                accept="image/*"
                class="form-input">
            <img id="avatar-preview" src="#" alt="Ảnh đại diện" style="max-width: 150px; max-height: 150px; margin-top: 10px; display: none;">
        </div>
        <div class="form-group">
            <label class="form-label" for="password">Mật khẩu</label>
            <div class="password-input-container">
                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Nhập mật khẩu"
                    required
                    class="form-input">
                <span class="toggle-password" id="togglePassword">
                    Hiện </span>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label" for="confirmpassword">Xác nhận mật khẩu</label>
            <div class="password-input-container"> <input
                    type="password"
                    name="confirmpassword"
                    id="confirmpassword" placeholder="Xác nhận mật khẩu"
                    required
                    class="form-input">
                <span class="toggle-password" id="toggleConfirmPassword">
                    Hiện </span>
            </div>
        </div>
        <!-- Phần người giới thiệu -->
        <div class="form-group">
            <label class="form-label">Người giới thiệu</label>
            <input
                type="email"
                name="referrer"
                placeholder="Nhập email người giới thiệu"
                class="form-input">
        </div>
        <button type="submit" class="submit-button">
            Đăng ký
        </button>
        <div class="auth-links">
            <a href="Login.php">Bạn đã có tài khoản? Đăng nhập</a>
        </div>
    </form>
</body>
<!-- Phần xử lý Hiện, Ẩn của Mật khẩu, Avatar (File) -->
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


        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordInput = document.getElementById('confirmpassword');

        if (toggleConfirmPassword && confirmPasswordInput) {
            toggleConfirmPassword.addEventListener('click', function() {
                const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPasswordInput.setAttribute('type', type);

                if (type === 'password') {
                    toggleConfirmPassword.textContent = 'Hiện';
                } else {
                    toggleConfirmPassword.textContent = 'Ẩn';
                }
            });
        }


        const avatarInput = document.getElementById('avatar');
        const avatarPreview = document.getElementById('avatar-preview');

        if (avatarInput) {
            avatarInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        avatarPreview.src = e.target.result;
                        avatarPreview.style.display = 'block';
                    };
                    reader.readAsDataURL(this.files[0]);
                } else {
                    avatarPreview.src = '#';
                    avatarPreview.style.display = 'none';
                }
            });
        }
    });
</script>

</html>
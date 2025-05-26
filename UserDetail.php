<?php
session_start();
require_once 'includes/dbh.inc.php';

$userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$isLoggedIn = isset($_SESSION['user_id']);
$canEditThisUser = false;
$canEditRole = true;
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success') {
        $message = '<p class="success-message">Cập nhật thông tin người dùng thành công!</p>';
    } elseif ($_GET['status'] === 'error') {
        $msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : 'Có lỗi xảy ra khi cập nhật thông tin người dùng.';
        $message = '<p class="error-message">' . $msg . '</p>';
    }
}

if ($userId > 0) {
    $sql = "SELECT
                u.*,
                r.RoleName,
                ref.Email AS ReferrerEmail
            FROM
                users AS u
            LEFT JOIN
                roles AS r ON u.RoleId = r.RoleId
            LEFT JOIN
                users AS ref ON u.ReferrerId = ref.UserId
            WHERE
                u.UserId = :userid";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userid', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $userDetail = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userDetail) {
        header("Location: index.php?status=error&msg=" . urlencode('Không tìm thấy người dùng.'));
        exit();
    }


    if ($isLoggedIn) {
        $rolesSql = "SELECT RoleId, RoleName FROM roles ORDER BY RoleName ASC";
        $rolesStmt = $pdo->query($rolesSql);
        $allRoles = $rolesStmt->fetchAll(PDO::FETCH_ASSOC);
        $loggedInUserId = $_SESSION['user_id'];
        $loggedInUserRoleId = null;
        $stmtLoggedInUser = $pdo->prepare("SELECT RoleId FROM users WHERE UserId = :loggedInUserId");
        $stmtLoggedInUser->bindParam(':loggedInUserId', $loggedInUserId, PDO::PARAM_INT);
        $stmtLoggedInUser->execute();
        $loggedInUserData = $stmtLoggedInUser->fetch(PDO::FETCH_ASSOC);
        if ($loggedInUserData) {
            $loggedInUserRoleId = $loggedInUserData['RoleId'];
        }

        if ($loggedInUserId == $userId) {
            $canEditThisUser = true;
        } elseif ($loggedInUserRoleId == 3) {

            if ($userDetail['RoleId'] == 3) {
                $canEditThisUser = false;
            } elseif ($userDetail['RoleId'] == 4) {
                $canEditThisUser = false;
            } else {
                $canEditThisUser = true;
            }
        } else if ($loggedInUserRoleId == 2) {
            if ($userDetail['RoleId'] == 4 || $userDetail['RoleId'] == 3 || $userDetail['RoleId'] == 2) {
                $canEditThisUser = false;
            }
        } else if ($loggedInUserRoleId == 4) {
            if ($userDetail['RoleId'] == 3 || $userDetail['RoleId'] == 2) {
                $canEditThisUser = true;
            }
        }
        if ($loggedInUserRoleId == 2 || $loggedInUserRoleId == 3) {
            $canEditRole = false;
        }
    }
} else {
    header("Location: index.php?status=error&msg=" . urlencode('ID người dùng không hợp lệ.'));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin chi tiết người dùng</title>
    <link rel="stylesheet" href="./css/UserDetail.module.css">
    <link rel="stylesheet" href="./css/reset.css">
</head>

<body>
    <div class="user-detail-container">
        <?php if ($userDetail): ?>
            <h5>Thông tin chi tiết người dùng</h5>

            <?php if (!empty($message)) : ?>
                <?= $message ?>
            <?php endif; ?>

            <?php if (!$isLoggedIn): ?>
                <p class="info-message">Vui lòng <a href="Login.php">đăng nhập</a> để xem thông tin đầy đủ.</p>
                <div class="user-info-limited">
                    <div class="form-group">
                        <label for="avatarupdate">Avatar:</label>
                        <?php if (!empty($userDetail['Avatar'])): ?>
                            <img src="<?= htmlspecialchars($userDetail['Avatar']) ?>" alt="Avatar" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; display: block; margin-bottom: 10px;">
                        <?php else: ?>
                            <img src="images/2.jpg" alt="Default Avatar" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; display: block; margin-bottom: 10px;">
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="fullName">Họ tên:</label>
                        <input type="text" id="fullName" name="fullName" value="<?= htmlspecialchars($userDetail['FullName']) ?>" readonly>
                    </div>
                    <a href="index.php" class="back-button">Quay lại danh sách</a>
                </div>

            <?php else: ?>
                <form action="UserDetailHandle.php" method="POST" class="user-update-form" enctype="multipart/form-data">
                    <input type="hidden" name="userId" value="<?= htmlspecialchars($userDetail['UserId']) ?>">

                    <div class="form-group">
                        <label for="displayUserId">ID:</label>
                        <input type="text" id="displayUserId" value="<?= htmlspecialchars($userDetail['UserId']) ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="avatarupdate">Avatar:</label>
                        <?php if (!empty($userDetail['Avatar'])): ?>
                            <img src="<?= htmlspecialchars($userDetail['Avatar']) ?>" alt="Avatar" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; display: block; margin-bottom: 10px;">
                        <?php else: ?>
                            <img src="images/2.jpg" alt="Default Avatar" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; display: block; margin-bottom: 10px;">
                        <?php endif; ?>
                        <input
                            type="file"
                            id="avatarupdate"
                            name="avatarupdate" accept="image/*"
                            class="form-input"
                            <?= $canEditThisUser ? '' : 'disabled' ?>>
                        <img id="avatarupdate-preview" src="#" alt="Ảnh đại diện mới" style="max-width: 150px; max-height: 150px; margin-top: 10px; display: none;">
                    </div>

                    <div class="form-group">
                        <label for="fullName">Họ tên:</label>
                        <input type="text" id="fullName" name="fullName" value="<?= htmlspecialchars($userDetail['FullName']) ?>" required <?= $canEditThisUser ? '' : 'readonly' ?>>
                    </div>

                    <div class="form-group">
                        <label for="userName">Tên đăng nhập:</label>
                        <input type="text" id="userName" name="userName" value="<?= htmlspecialchars($userDetail['UserName']) ?>" required <?= $canEditThisUser ? '' : 'readonly' ?>>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($userDetail['Email']) ?>" required <?= $canEditThisUser ? '' : 'readonly' ?>>
                    </div>

                    <div class="form-group">
                        <label for="phone">Số điện thoại:</label>
                        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($userDetail['Phone']) ?>" required <?= $canEditThisUser ? '' : 'readonly' ?>>
                    </div>

                    <div class="form-group">
                        <label for="birthday">Ngày sinh:</label>
                        <input type="date" id="birthday" name="birthday" value="<?= htmlspecialchars($userDetail['Birthday']) ?>" <?= $canEditThisUser ? '' : 'readonly' ?>>
                    </div>

                    <div class="form-group">
                        <label>Giới tính:</label>
                        <div class="radio-group">
                            <label>
                                <input type="radio" name="gender" value="Nam" <?= ($userDetail['Gender'] == 'Nam') ? 'checked' : '' ?> <?= $canEditThisUser ? '' : 'disabled' ?>> Nam
                            </label>
                            <label>
                                <input type="radio" name="gender" value="Nữ" <?= ($userDetail['Gender'] == 'Nữ') ? 'checked' : '' ?> <?= $canEditThisUser ? '' : 'disabled' ?>> Nữ
                            </label>
                            <label>
                                <input type="radio" name="gender" value="Khác" <?= ($userDetail['Gender'] == 'Khác') ? 'checked' : '' ?> <?= $canEditThisUser ? '' : 'disabled' ?>> Khác
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="joinedDate">Ngày tham gia:</label>
                        <input type="text" id="joinedDate" name="joinedDate" value="<?= htmlspecialchars($userDetail['JoinedDate']) ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="roleId">Vai trò:</label>
                        <select id="roleId" name="roleId" required <?= $canEditThisUser ? '' : 'disabled' ?> <?= $canEditRole ? '' : 'disabled' ?>>>
                            <?php foreach ($allRoles as $role): ?>
                                <option value="<?= htmlspecialchars($role['RoleId']) ?>"
                                    <?= ($role['RoleId'] == $userDetail['RoleId']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($role['RoleName']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!$canEditThisUser || !$canEditRole): ?>
                            <input type="hidden" name="roleId" value="<?= htmlspecialchars($userDetail['RoleId']) ?>">
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="referrerEmail">Người giới thiệu (Email):</label>
                        <input type="email" id="referrerEmail" name="referrerEmail" value="<?= htmlspecialchars($userDetail['ReferrerEmail']) ?>" <?= $canEditThisUser ? '' : 'readonly' ?>>
                        <small>Nhập email của người giới thiệu nếu có.</small>
                    </div>

                    <div class="form-group">
                        <label for="description">Mô tả:</label>
                        <textarea id="description" name="description" <?= $canEditThisUser ? '' : 'readonly' ?>><?= htmlspecialchars($userDetail['Description']) ?></textarea>
                    </div>

                    <?php if ($canEditThisUser): ?>
                        <button type="submit" name="update_user" class="submit-button">Lưu</button>
                    <?php endif; ?>
                    <a href="index.php" class="back-button">Quay lại danh sách</a>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <p>Không tìm thấy thông tin người dùng.</p>
            <div class="back-button">
                <a href="index.php">Quay lại danh sách</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        const avatarInput = document.getElementById('avatarupdate');
        const avatarPreview = document.getElementById('avatarupdate-preview');

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
    </script>
</body>

</html>
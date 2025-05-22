<?php
require_once 'includes/dbh.inc.php';

$userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';


if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success') {
        $message = '<p class="success-message">Cập nhật thông tin người dùng thành công!</p>';
    } elseif ($_GET['status'] === 'error') {
        $message = '<p class="error-message">Có lỗi xảy ra khi cập nhật thông tin người dùng.</p>';
        if (isset($_GET['msg'])) {
            $message .= ' ' . htmlspecialchars($_GET['msg']);
        }
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


    $rolesSql = "SELECT RoleId, RoleName FROM roles ORDER BY RoleName ASC";
    $rolesStmt = $pdo->query($rolesSql);
    $allRoles = $rolesStmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Cập nhật thông tin người dùng</title>
    <link rel="stylesheet" href="./css/main.css">
    <link rel="stylesheet" href="./css/reset.css">
</head>

<body>
    <div class="user-detail-container">
        <?php if ($userDetail): ?>
            <h5>Cập nhật thông tin người dùng</h5>

            <?php if (!empty($message)) : ?>
                <?= $message ?>
            <?php endif; ?>

            <form action="UserDetailHandle.php" method="POST" class="user-update-form">
                <input type="hidden" name="userId" value="<?= htmlspecialchars($userDetail['UserId']) ?>">

                <div class="form-group">
                    <label for="displayUserId">ID:</label>
                    <input type="text" id="displayUserId" value="<?= htmlspecialchars($userDetail['UserId']) ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="avatar">Avatar:</label>
                    <?php if (!empty($userDetail['Avatar'])): ?>
                        <img src="<?= htmlspecialchars($userDetail['Avatar']) ?>" alt="Avatar" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; display: block; margin-bottom: 10px;">
                    <?php else: ?>
                        <img src="./uploads/default_avatar.png" alt="Default Avatar" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; display: block; margin-bottom: 10px;">
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="fullName">Họ tên:</label>
                    <input type="text" id="fullName" name="fullName" value="<?= htmlspecialchars($userDetail['FullName']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="userName">Tên đăng nhập:</label>
                    <input type="text" id="userName" name="userName" value="<?= htmlspecialchars($userDetail['UserName']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($userDetail['Email']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone">Số điện thoại:</label>
                    <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($userDetail['Phone']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="birthday">Ngày sinh:</label>
                    <input type="date" id="birthday" name="birthday" value="<?= htmlspecialchars($userDetail['Birthday']) ?>">
                </div>

                <div class="form-group">
                    <label>Giới tính:</label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="gender" value="Nam" <?= ($userDetail['Gender'] == 'Nam') ? 'checked' : '' ?>> Nam
                        </label>
                        <label>
                            <input type="radio" name="gender" value="Nữ" <?= ($userDetail['Gender'] == 'Nữ') ? 'checked' : '' ?>> Nữ
                        </label>
                        <label>
                            <input type="radio" name="gender" value="Khác" <?= ($userDetail['Gender'] == 'Khác') ? 'checked' : '' ?>> Khác
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="joinedDate">Ngày tham gia:</label>
                    <input type="text" id="joinedDate" name="joinedDate" value="<?= htmlspecialchars($userDetail['JoinedDate']) ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="roleId">Vai trò:</label>
                    <select id="roleId" name="roleId" required>
                        <?php foreach ($allRoles as $role): ?>
                            <option value="<?= htmlspecialchars($role['RoleId']) ?>"
                                <?= ($role['RoleId'] == $userDetail['RoleId']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($role['RoleName']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="referrerEmail">Người giới thiệu (Email):</label>
                    <input type="email" id="referrerEmail" name="referrerEmail" value="<?= htmlspecialchars($userDetail['ReferrerEmail']) ?>">
                    <small>Nhập email của người giới thiệu nếu có.</small>
                </div>

                <div class="form-group">
                    <label for="description">Mô tả:</label>
                    <textarea id="description" name="description"><?= htmlspecialchars($userDetail['Description']) ?></textarea>
                </div>

                <button type="submit" name="update_user" class="submit-button">Lưu thay đổi</button>
                <a href="index.php" class="back-button">Quay lại danh sách</a>
            </form>
        <?php else: ?>
            <p>Không tìm thấy thông tin người dùng.</p>
            <div class="back-button">
                <a href="index.php">Quay lại danh sách</a>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>
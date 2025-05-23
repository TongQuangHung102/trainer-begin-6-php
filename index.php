<?php
require_once 'includes/dbh.inc.php';


$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
if (!in_array($limit, [10, 20, 50, 100])) {
    $limit = 10;
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;


$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'JoinedDate';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'DESC';


$allowed_sort_columns = ['FullName', 'JoinedDate'];
if (!in_array($sort_by, $allowed_sort_columns)) {
    $sort_by = 'JoinedDate';
}


if (!in_array(strtoupper($sort_order), ['ASC', 'DESC'])) {
    $sort_order = 'DESC';
}


$sql = "SELECT
            u.UserId,
            u.FullName,
            u.UserName,
            u.Email,
            u.Phone,
            u.Avatar,
            u.Password,
            u.Description,
            u.Birthday,
            u.Gender,
            u.JoinedDate,
            r.RoleName,
            ref.Email AS ReferrerEmail
        FROM
            users AS u
        LEFT JOIN
            roles AS r ON u.RoleId = r.RoleId
        LEFT JOIN
            users AS ref ON u.ReferrerId = ref.UserId
        ORDER BY " . $sort_by . " " . $sort_order . "
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);


$total_users_sql = "SELECT COUNT(*) FROM users";
$total_stmt = $pdo->query($total_users_sql);
$total_users = $total_stmt->fetchColumn();
$total_pages = ceil($total_users / $limit);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/UserList.module.css">
    <link rel="stylesheet" href="./css/reset.css">
    <title>HomePage</title>

</head>

<body>

    <div class="user-list">
        <h5>Danh sách người dùng</h5>

        <div class="controls-container">
            <div class="pagination-controls">
                <span>Hiển thị:</span>
                <select onchange="window.location.href = this.value">
                    <?php
                    $limits = [10, 20, 50, 100];
                    foreach ($limits as $l) {
                        $selected = ($l == $limit) ? 'selected' : '';
                        echo "<option value=\"?page=1&limit={$l}&sort_by={$sort_by}&sort_order={$sort_order}\" {$selected}>{$l}</option>";
                    }
                    ?>
                </select>
                <span>người dùng mỗi trang</span>
            </div>

            <div class="sort-controls">
                <span>Sắp xếp theo:</span>
                <select onchange="window.location.href = this.value">
                    <?php
                    $sort_options = [
                        'FullName ASC' => 'Tên (A-Z)',
                        'FullName DESC' => 'Tên (Z-A)',
                        'JoinedDate ASC' => 'Ngày tham gia (Cũ nhất)',
                        'JoinedDate DESC' => 'Ngày tham gia (Mới nhất)'
                    ];
                    foreach ($sort_options as $value => $label) {
                        $selected = (($sort_by . ' ' . $sort_order) == $value) ? 'selected' : '';
                        $parts = explode(' ', $value);
                        $s_by = $parts[0];
                        $s_order = $parts[1];
                        echo "<option value=\"?page={$page}&limit={$limit}&sort_by={$s_by}&sort_order={$s_order}\" {$selected}>{$label}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="user-row user-header">
            <div>UserID</div>
            <div>Avatar</div>
            <div>Họ tên</div>
            <div>Người giới thiệu</div>
            <div>Ngày tham gia</div>
            <div>Hành động</div>
        </div>

        <?php if (empty($users)): ?>
            <div class="user-row">
                <div style="grid-column: 1 / -1; text-align: center;">Không có người dùng nào để hiển thị.</div>
            </div>
        <?php else: ?>
            <?php foreach ($users as $user): ?>
                <div class="user-row">
                    <div><?= htmlspecialchars($user['UserId']) ?></div>
                    <div>
                        <?php if (!empty($user['Avatar'])): ?>
                            <img src="<?= htmlspecialchars($user['Avatar']) ?>" alt="Avatar của <?= htmlspecialchars($user['UserName']) ?>" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                        <?php else: ?>
                            <img src="./uploads/default_avatar.png">
                        <?php endif; ?>
                    </div>
                    <div><?= htmlspecialchars($user['FullName']) ?></div>
                    <div><?= htmlspecialchars($user['ReferrerEmail'] ?: 'Không có') ?></div>
                    <div><?= htmlspecialchars($user['JoinedDate']) ?></div>
                    <div>
                        <a href="UserDetail.php?id=<?= htmlspecialchars($user['UserId']) ?>">
                            <button class="buttonEdit">Chi tiết</button>
                        </a>
                        <button class="buttonDel">Xóa</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="pagination-controls" style="justify-content: center; margin-top: 20px;">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>&sort_by=<?= $sort_by ?>&sort_order=<?= $sort_order ?>">Trước</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>&limit=<?= $limit ?>&sort_by=<?= $sort_by ?>&sort_order=<?= $sort_order ?>" class="<?= ($i == $page) ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>&sort_by=<?= $sort_by ?>&sort_order=<?= $sort_order ?>">Sau</a>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
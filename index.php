<?php
session_start();
require_once 'includes/dbh.inc.php';
// ?? 0: Đây là toán tử null coalescing
// $_SESSION['user_role_id'] tồn tại và không null, thì gán giá trị đó cho $currentUserRoleId; ngược lại, gán giá trị 0. 
$currentUserRoleId = $_SESSION['user_role_id'] ?? 0;
// Xác định số lượng người dùng sẽ hiển thị trên mỗi trang ($limit).
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
// Kiểm tra xem $limit có không nằm trong mảng các giá trị được phép (10, 20, 50, 100) hay không.
if (!in_array($limit, [10, 20, 50, 100])) {
    // Nếu giá trị $limit không hợp lệ, nó sẽ bị đặt lại về 10.
    $limit = 10;
}

// Xác định số trang hiện tại.
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
// OFFSET cho biết số lượng hàng cần bỏ qua từ đầu kết quả trước khi bắt đầu lấy dữ liệu.
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
        <!-- Kiểm tra người dùng đã đăng nhập chưa bằng biến $_SESSION['user_id']
        1. Đã đăng nhập => Nút "Đăng xuất" hiển thị.
        2. Chưa đăng nhập => Nút "Đăng nhập" hiển thị. 
        -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="LogOutHandle.php" class="logout-button">Đăng xuất</a>
        <?php else: ?>
            <a href="Login.php" class="loginguest-button">Đăng nhập</a>
        <?php endif; ?>

        <div class="controls-container">
            <!-- Filter danh sách số lượng hiển thị người dùng trên 1 trang -->
            <div class="pagination-controls">
                <span>Hiển thị:</span>
                <!-- Tạo Drowdown list -->
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

            <!-- Filter danh sách người dùng trên 1 trang hiển thị theo FullName or JoinedDate -->
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
                    // $value sẽ chứa khóa (ví dụ: FullName ASC).
                    // $label sẽ chứa giá trị hiển thị (ví dụ: Tên (A-Z)).
                    foreach ($sort_options as $value => $label) {
                        $selected = (($sort_by . ' ' . $sort_order) == $value) ? 'selected' : '';
                        // Dòng này sử dụng hàm explode() để chia chuỗi $value (ví dụ: FullName ASC) thành một mảng dựa trên ký tự khoảng trắng (' ').
                        // VD: ['FullName', 'ASC']
                        $parts = explode(' ', $value);
                        // VD: FullName
                        $s_by = $parts[0];
                        // VD: ASC
                        $s_order = $parts[1];
                        echo "<option value=\"?page={$page}&limit={$limit}&sort_by={$s_by}&sort_order={$s_order}\" {$selected}>{$label}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        <!-- Header danh sách người dùng -->
        <div class="user-row user-header">
            <div>UserID</div>
            <div>Avatar</div>
            <div>Họ tên</div>
            <div>Người giới thiệu</div>
            <div>Ngày tham gia</div>
            <div>Hành động</div>
        </div>
        <!-- Container danh sách người dùng -->
        <?php if (empty($users)): ?>
            <div class="user-row">
                <!-- grid-column: 1 / -1; 
                1. 1: Đại diện cho đường lưới (grid line) bắt đầu đầu tiên của lưới.
                2. -1: Đại diện cho đường lưới kết thúc cuối cùng của lưới. -->
                <div style="grid-column: 1 / -1; text-align: center;">Không có người dùng nào để hiển thị.</div>
            </div>
        <?php else: ?>
            <?php foreach ($users as $user): ?>
                <div class="user-row">
                    <div><?= htmlspecialchars($user['UserId']) ?></div>
                    <div>
                        <?php if (!empty($user['Avatar'])): ?>
                            <!-- object-fit: cover
                             1. Ảnh sẽ giữ tỷ lệ gốc
                             2. Ảnh sẽ được phóng to hoặc thu nhỏ sao cho đầy khung chứa.-->
                            <img src="<?= htmlspecialchars($user['Avatar']) ?>" alt="Avatar của <?= htmlspecialchars($user['UserName']) ?>" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                        <?php else: ?>
                            <img src="images/2.jpg">
                        <?php endif; ?>
                    </div>
                    <div><?= htmlspecialchars($user['FullName']) ?></div>
                    <div><?= htmlspecialchars($user['ReferrerEmail'] ?: 'Không có') ?></div>
                    <div><?= htmlspecialchars($user['JoinedDate']) ?></div>
                    <div>
                        <a href="UserDetail.php?id=<?= htmlspecialchars($user['UserId']) ?>">
                            <button class="buttonEdit">Chi tiết</button>
                        </a>
                        <!-- Role là admod, admin thì mới hiển thị button Xóa -->
                        <?php
                        if ($currentUserRoleId == 3 || $currentUserRoleId == 4):
                        ?>
                            <a href="DeleteUserHandle.php?id=<?= htmlspecialchars($user['UserId']) ?>" onclick="return confirm('Bạn có chắc muốn xóa người dùng này?')">
                                <button class="buttonDel">Xóa</button>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <!-- Phân trang  -->
        <div class="pagination-controls" style="justify-content: center; margin-top: 20px;">
            <!-- Kiểm tra giá trị $page có lớn hơn 1 hay không => Hiển thị "Trước"-->
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>&sort_by=<?= $sort_by ?>&sort_order=<?= $sort_order ?>">Trước</a>
            <?php endif; ?>
            <!-- Vòng lặp tạo này sẽ tạo ra các liên kết số trang -->
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>&limit=<?= $limit ?>&sort_by=<?= $sort_by ?>&sort_order=<?= $sort_order ?>" class="<?= ($i == $page) ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
            <!-- Kiểm tra giá trị $page có nhỏ hơn tổng số trang $total_pages hay không => Hiển thị "Sau"-->
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>&sort_by=<?= $sort_by ?>&sort_order=<?= $sort_order ?>">Sau</a>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
<?php
session_start();

require_once "includes/dbh.inc.php";


$loggedInUserRole = $_SESSION['user_role_id'] ?? 0;

if (!isset($loggedInUserRole) || ($loggedInUserRole !== 3 && $loggedInUserRole !== 4)) {
    header("Location: index.php?delete=unauthorized");
    exit();
}

if (isset($_GET['id']) && (int) ($_GET['id'])) {
    $user_id_to_delete = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id_to_delete) {
        header("Location: index.php?delete=selfdelete");
        exit();
    }
    try {
        $queryGetRole = "SELECT RoleId FROM users WHERE UserId = :id;";
        $stmtGetRole = $pdo->prepare($queryGetRole);
        $stmtGetRole->bindParam(':id', $user_id_to_delete, PDO::PARAM_INT);
        $stmtGetRole->execute();
        $userToDelete = $stmtGetRole->fetch(PDO::FETCH_ASSOC);

        if (!$userToDelete) {
            header("Location: index.php?delete=notfound");
            exit();
        }
        $roleOfUserToDelete = $userToDelete['RoleId'];
        $canDelete = false;
        if ($loggedInUserRole === 3) {
            if ($roleOfUserToDelete === 2) {
                $canDelete = true;
            }
        } elseif ($loggedInUserRole === 4) {
            if ($roleOfUserToDelete !== 4) {
                $canDelete = true;
            }
        }
        if (!$canDelete) {
            header("Location: index.php?delete=permissiondenied");
            exit();
        }


        $queryDelete = "DELETE FROM users WHERE UserId = :id;";
        $stmtDelete = $pdo->prepare($queryDelete);
        $stmtDelete->bindParam(':id', $user_id_to_delete, PDO::PARAM_INT);
        $stmtDelete->execute();

        if ($stmtDelete->rowCount() > 0) {
            header("Location: index.php?delete=success");
            exit();
        } else {
            header("Location: index.php?delete=notfound");
            exit();
        }
    } catch (PDOException $e) {
        die("Đã xảy ra lỗi hệ thống khi xóa người dùng. Vui lòng thử lại sau.");
    } finally {
        $pdo = null;
        $stmtGetRole = null;
        $stmtDelete = null;
    }
} else {

    header("Location: index.php?delete=invalidid");
    exit();
}

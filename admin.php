<?php
session_start();
require_once __DIR__ . '/configs/env.php';
require_once __DIR__ . '/configs/helper.php';
require_once __DIR__ . '/configs/pdo.php';

require_admin();

$pageTitle = 'Trang quản lý';
$user = $_SESSION['user'];
$message = null;
$errors = [];

$conn = get_connection();
if (!$conn) {
    $errors[] = 'Không thể kết nối tới cơ sở dữ liệu.';
} else {
    ensure_tour_items_table($conn);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($title === '') {
            $errors[] = 'Tiêu đề không được để trống.';
        }
        if ($description === '') {
            $errors[] = 'Mô tả không được để trống.';
        }

        if (empty($errors)) {
            if (add_tour_item($conn, $title, $description)) {
                $message = 'Đã thêm dữ liệu thành công. Người dùng sẽ thấy ngay.';
            } else {
                $errors[] = 'Thêm dữ liệu thất bại. Vui lòng thử lại.';
            }
        }
    }

    $items = get_tour_items($conn);
}

require_once PATH_VIEW . 'admin/admin.php';

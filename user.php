<?php
session_start();
require_once __DIR__ . '/configs/env.php';
require_once __DIR__ . '/configs/helper.php';
require_once __DIR__ . '/configs/pdo.php';

require_user();

$pageTitle = 'Trang người dùng';
$user = $_SESSION['user'];
$error = null;
$items = [];

$conn = get_connection();
if ($conn) {
    ensure_tour_items_table($conn);
    $items = get_tour_items($conn);
} else {
    $error = 'Không thể kết nối tới cơ sở dữ liệu để tải dữ liệu.';
}

require_once PATH_VIEW . 'user/user.php';

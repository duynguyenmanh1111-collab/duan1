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
$bookings = [];
$message = null;
$bookingError = null;

if ($conn) {
    ensure_tour_items_table($conn);
    ensure_bookings_table($conn);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'place_booking') {
        $tourId = intval($_POST['tour_id'] ?? 0);
        $bookingDate = trim($_POST['booking_date'] ?? '');
        $quantity = max(1, intval($_POST['quantity'] ?? 1));

        if ($tourId <= 0 || $bookingDate === '') {
            $bookingError = 'Vui lòng chọn tour và ngày khởi hành.';
        } else {
            $tour = get_tour_by_id($conn, $tourId);

            if (!$tour) {
                $bookingError = 'Tour không tồn tại.';
            } else {
                $userId = intval($user['id']);
                $userName = $user['username'] ?? ($user['name'] ?? 'Người dùng');
                if (add_booking($conn, $userId, $userName, $tourId, $tour['title'], $bookingDate, $quantity)) {
                    $message = 'Đặt tour thành công! Admin sẽ liên hệ xác nhận.';
                } else {
                    $bookingError = 'Đặt tour thất bại. Vui lòng thử lại.';
                }
            }
        }
    }

    $items = get_tour_items($conn);
    $bookings = get_user_bookings($conn, intval($user['id']));
} else {
    $error = 'Không thể kết nối tới cơ sở dữ liệu để tải dữ liệu.';
}

require_once PATH_VIEW . 'user/user.php';
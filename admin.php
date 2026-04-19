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
    ensure_bookings_table($conn);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // =============================
        // 👉 XÓA BOOKING
        // =============================
        if (isset($_POST['action']) && $_POST['action'] === 'delete_booking') {

            $bookingId = intval($_POST['booking_id']);

            if ($bookingId > 0) {

                $booking = get_booking_by_id($conn, $bookingId);

                if ($booking && $booking['payment_status'] === 'Đã thanh toán') {
                    $errors[] = 'Không thể xóa booking đã thanh toán.';
                } else {
                    delete_booking($conn, $bookingId);
                    $message = 'Xóa booking thành công!';
                }

            } else {
                $errors[] = 'ID không hợp lệ.';
            }
        }

        // =============================
        // 👉 CẬP NHẬT TRẠNG THÁI
        // =============================
        elseif (isset($_POST['action']) && $_POST['action'] === 'update_booking_status') {

            $bookingId = intval($_POST['booking_id']);
            $status = $_POST['status'] ?? '';

            if ($bookingId > 0 && $status !== '') {

                $booking = get_booking_by_id($conn, $bookingId);

                if ($status === 'Đã xác nhận' && $booking['payment_status'] !== 'Đã thanh toán') {
                    $errors[] = 'Không thể xác nhận khi chưa thanh toán.';
                } else {
                    update_booking_status($conn, $bookingId, $status);
                    $message = 'Cập nhật trạng thái booking thành công!';
                }

            } else {
                $errors[] = 'Dữ liệu không hợp lệ.';
            }
        }

        // =============================
        // 👉 THÊM TOUR
        // =============================
        elseif (isset($_POST['action']) && $_POST['action'] === 'add_tour') {

            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');

            // ✅ FIX QUAN TRỌNG
            $image = trim($_POST['image'] ?? ''); // nhập: dubai.jpg
            $category = trim($_POST['category'] ?? '');
            $price = intval($_POST['price'] ?? 0);

            if ($title === '') {
                $errors[] = 'Tiêu đề không được để trống.';
            }

            if (empty($errors)) {
                if (add_tour_item($conn, $title, $description, $image, $category, $price)) {
                    $message = 'Thêm tour thành công!';
                } else {
                    $errors[] = 'Thêm thất bại.';
                }
            }
        }
    }

    // 👉 dữ liệu
    $items = get_tour_items($conn);
    $bookings = get_all_bookings($conn);
}

require_once PATH_VIEW . 'admin/admin.php';
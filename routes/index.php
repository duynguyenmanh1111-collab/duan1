<?php
// 1. Require các cấu hình hệ thống
require_once 'configs/env.php';
require_once 'configs/helper.php';
require_once 'configs/pdo.php';

// 2. Require các Models
require_once 'models/BaseModel.php';
require_once 'models/TourModel.php';

// 3. Require các Controllers
require_once 'controllers/HomeController.php';
require_once 'controllers/AdminTourController.php';

// 4. Lấy tham số act từ URL (mặc định là '/' nếu không có)
$act = $_GET['act'] ?? '/';

// 5. Điều hướng (Routing)
switch ($act) {
    // --- ROUTE CHO TRANG CHỦ ---
    case '/':
        (new HomeController())->index();
        break;

    // --- ROUTE QUẢN LÝ TOUR (ADMIN) ---

    // Hiển thị danh sách Tour
    case 'list-tour':
        (new AdminTourController())->index();
        break;

    // Xử lý thêm Tour mới
    case 'add-tour':
        (new AdminTourController())->store();
        break;

    // Xử lý cập nhật Tour
    case 'update-tour':
        (new AdminTourController())->update();
        break;

    // Xử lý xóa Tour
    case 'delete-tour':
        $id = $_GET['id'] ?? null;
        if ($id) {
            (new AdminTourController())->destroy($id);
        } else {
            header("Location: " . BASE_URL . "?act=list-tour");
        }
        break;

    // Xem chi tiết một Tour
    case 'tour-detail':
        $id = $_GET['id'] ?? null;
        if ($id) {
            (new AdminTourController())->show($id);
        } else {
            echo "ID Tour không hợp lệ!";
        }
        break;

    // --- CÁC ROUTE KHÁC (USER, AUTH...) ---
    case 'login':
        // Gọi controller xử lý đăng nhập
        break;

    case 'logout':
        // Gọi controller xử lý đăng xuất
        break;

    // Trang 404
    default:
        echo "404 - Trang không tồn tại!";
        break;
}
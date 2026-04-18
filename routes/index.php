<?php
// 1. Require các cấu hình hệ thống
require_once 'configs/env.php';
require_once 'configs/helper.php';
require_once 'configs/pdo.php';

// 2. Require các Models
require_once 'models/BaseModel.php';
require_once 'models/TourModel.php';

// 3. Require các Controllers 
// (Đảm bảo file HomeController.php và AdminTourController.php có dòng: namespace Controllers;)
require_once 'controllers/HomeController.php';
require_once 'controllers/AdminTourController.php';

use Controllers\HomeController;
use Controllers\AdminTourController;

// 4. Lấy tham số act từ URL
$act = $_GET['act'] ?? '/';

// 5. Điều hướng (Routing)
switch ($act) {
    // --- ROUTE CHO TRANG CHỦ ---
    case '/':
        (new HomeController())->index();
        break;

    // --- ROUTE QUẢN LÝ TOUR (ADMIN) ---
    case 'list-tour':
        (new AdminTourController())->index();
        break;

    case 'add-tour':
        (new AdminTourController())->store();
        break;

    case 'update-tour':
        (new AdminTourController())->update();
        break;

    case 'delete-tour':
        $id = $_GET['id'] ?? null;
        if ($id) {
            (new AdminTourController())->destroy($id);
        } else {
            header("Location: " . BASE_URL . "?act=list-tour");
        }
        break;

    case 'tour-detail':
        $id = $_GET['id'] ?? null;
        if ($id) {
            (new AdminTourController())->show($id);
        } else {
            echo "ID Tour không hợp lệ!";
        }
        break;

    // --- CÁC ROUTE KHÁC ---
    case 'login':
        // Code login...
        break;

    case 'logout':
        // Code logout...
        break;

    default:
        echo "404 - Trang không tồn tại!";
        break;
}
<?php

namespace Controllers; // BẮT BUỘC PHẢI CÓ DÒNG NÀY Ở ĐẦU FILE

class HomeController
{
    public function index()
    {
        // Chú ý: Nếu dùng SESSION thì ở file index.php bạn nên có session_start() rồi nhé
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . 'login.php');
            exit;
        }

        $title = 'Trang chủ';
        $user = $_SESSION['user'];

        require_once PATH_VIEW . 'main.php';
    }

    public function about()
    {
        require_once PATH_VIEW . 'about.php';
    }
}
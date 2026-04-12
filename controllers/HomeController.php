<?php

class HomeController
{
    public function index()
    {
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

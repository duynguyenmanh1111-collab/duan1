<?php
session_start();
require_once __DIR__ . '/configs/env.php';

// Xóa biến user
unset($_SESSION['user']);

// Hủy toàn bộ session
session_destroy();

// Chuyển hướng
header('Location: ' . BASE_URL . 'login.php');
exit;
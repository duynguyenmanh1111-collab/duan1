<?php
session_start();
require_once __DIR__ . '/configs/env.php';
require_once __DIR__ . '/configs/helper.php';

require_admin();

$pageTitle = 'Trang quản lý';
$user = $_SESSION['user'];

require_once PATH_VIEW . 'admin/admin.php';

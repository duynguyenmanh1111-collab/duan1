<?php
session_start();
require_once __DIR__ . '/configs/env.php';
require_once __DIR__ . '/configs/helper.php';

require_user();

$pageTitle = 'Trang người dùng';
$user = $_SESSION['user'];

require_once PATH_VIEW . 'user/user.php';

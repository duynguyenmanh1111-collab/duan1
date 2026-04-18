<?php
session_start();
require_once __DIR__ . '/configs/env.php';

unset($_SESSION['user']);
header('Location: ' . BASE_URL . 'login.php');
exit;
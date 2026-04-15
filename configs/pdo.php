<?php
require_once 'env.php';

function get_connection()
{
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        return null;
    }
}

function ensure_tour_items_table(PDO $conn): void
{
    $sql = "CREATE TABLE IF NOT EXISTS tour_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $conn->exec($sql);
}

function add_tour_item(PDO $conn, string $title, string $description): bool
{
    $stmt = $conn->prepare("INSERT INTO tour_items (title, description) VALUES (?, ?)");
    return $stmt->execute([$title, $description]);
}

function get_tour_items(PDO $conn): array
{
    $stmt = $conn->query("SELECT id, title, description, created_at FROM tour_items ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
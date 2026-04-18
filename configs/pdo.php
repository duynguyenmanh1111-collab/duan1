<?php
require_once 'env.php';

function get_connection()
{
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8",
            DB_USERNAME,
            DB_PASSWORD
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        return null;
    }
}

/* ================== TOUR ================== */
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

function get_tour_items(PDO $conn): array
{
    $stmt = $conn->query("SELECT * FROM tour_items ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* ================== BOOKINGS ================== */
function ensure_bookings_table(PDO $conn): void
{
    $sql = "CREATE TABLE IF NOT EXISTS bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        customer_name VARCHAR(255),
        email VARCHAR(255),
        phone VARCHAR(20),
        tour_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (tour_id) REFERENCES tour_items(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $conn->exec($sql);
}

function get_user_bookings(PDO $conn, int $user_id): array
{
    $stmt = $conn->prepare("
        SELECT b.*, t.title 
        FROM bookings b
        LEFT JOIN tour_items t ON b.tour_id = t.id
        WHERE b.user_id = ?
        ORDER BY b.created_at DESC
    ");

    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
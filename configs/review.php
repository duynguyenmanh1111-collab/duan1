<?php
function add_review($conn, $userId, $tourId, $rating, $comment)
{
    $sql = "INSERT INTO reviews (user_id, tour_id, rating, comment) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$userId, $tourId, $rating, $comment]);
}

function get_reviews_by_tour($conn, $tourId)
{
    $sql = "SELECT * FROM reviews WHERE tour_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$tourId]);
    return $stmt->fetchAll();
}
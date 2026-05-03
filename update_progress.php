<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Unauthorized access");
}

$user_id   = intval($_SESSION['user_id']);
$course_id = intval($_POST['course_id']);
$video_id  = intval($_POST['video_id']);

if ($user_id > 0 && $video_id > 0) {
    // This query inserts a new record OR updates the existing one if it already exists
    $sql = "INSERT INTO video_progress (user_id, course_id, video_id, is_completed) 
            VALUES (?, ?, ?, 1) 
            ON DUPLICATE KEY UPDATE is_completed = 1, watched_at = NOW()";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $user_id, $course_id, $video_id);
    
    if ($stmt->execute()) {
        echo "SUCCESS_UPDATED";
    } else {
        echo "DB_ERROR";
    }
} else {
    echo "INVALID_DATA";
}
?>
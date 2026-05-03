<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $course_id = intval($_POST['course_id']);
    
    // Update status to 'completed'
    $stmt = $conn->prepare("UPDATE enrollments SET status = 'completed' WHERE user_id = ? AND course_id = ?");
    $stmt->bind_param("ii", $user_id, $course_id);
    
    if ($stmt->execute()) {
        header("Location: my_courses.php?tab=completed"); // Redirect to completed tab
    } else {
        echo "Error updating record.";
    }
}
?>
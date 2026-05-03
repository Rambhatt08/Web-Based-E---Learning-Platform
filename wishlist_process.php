<?php
session_start();
require 'db_connect.php';

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login to add to wishlist'); window.location.href='login.php';</script>";
    exit();
}

if (isset($_POST['course_id'])) {
    $user_id = $_SESSION['user_id'];
    $course_id = intval($_POST['course_id']);
    $redirect_to = isset($_POST['redirect']) ? $_POST['redirect'] : 'courses.php';

    // Check if already in wishlist
    $check = $conn->query("SELECT id FROM wishlist WHERE user_id = $user_id AND course_id = $course_id");

    if ($check->num_rows > 0) {
        // Remove it
        $conn->query("DELETE FROM wishlist WHERE user_id = $user_id AND course_id = $course_id");
    } else {
        // Add it
        $conn->query("INSERT INTO wishlist (user_id, course_id) VALUES ($user_id, $course_id)");
    }

    // Go back to the page user came from
    header("Location: " . $redirect_to);
    exit();
}
?>
<?php
// db_connect.php
$servername = "localhost";
$username   = "root";      // Default XAMPP username
$password   = "";          // Default XAMPP password is empty
$dbname     = "smartlearn_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/**
 * PROGRESS HELPER FUNCTION
 * Calculates the percentage of completed lectures for a specific course and user.
 */
function getCourseProgress($conn, $user_id, $course_id) {
    // 1. Get total number of lectures for the course
    $total_stmt = $conn->prepare("SELECT COUNT(*) as total FROM course_lectures WHERE course_id = ?");
    $total_stmt->bind_param("i", $course_id);
    $total_stmt->execute();
    $total_lectures = $total_stmt->get_result()->fetch_assoc()['total'];

    if ($total_lectures == 0) return 0; // Prevent division by zero

    // 2. Get number of completed lectures from our tracking table
    $comp_stmt = $conn->prepare("SELECT COUNT(*) as completed FROM video_progress 
                                 WHERE user_id = ? AND course_id = ? AND is_completed = 1");
    $comp_stmt->bind_param("ii", $user_id, $course_id);
    $comp_stmt->execute();
    $completed_lectures = $comp_stmt->get_result()->fetch_assoc()['completed'];

    // 3. Return rounded percentage
    return round(($completed_lectures / $total_lectures) * 100);
}
?>
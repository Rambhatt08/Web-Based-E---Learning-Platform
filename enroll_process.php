<?php
session_start();
require 'db_connect.php';

// Only allow logged-in users
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $course_id = intval($_POST['course_id']);

    // Check if already enrolled (Double check)
    $check = $conn->query("SELECT id FROM enrollments WHERE user_id=$user_id AND course_id=$course_id");
    
    if ($check->num_rows == 0) {
        // Insert Enrollment
        $sql = "INSERT INTO enrollments (user_id, course_id) VALUES ($user_id, $course_id)";
        if ($conn->query($sql)) {
            // Update the "Total Enrolled" count in courses table
            $conn->query("UPDATE courses SET total_enrolled = total_enrolled + 1 WHERE id = $course_id");
            
            echo "<script>alert('Enrollment Successful!'); window.location.href='courses.php';</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "<script>alert('You are already enrolled!'); window.location.href='courses.php';</script>";
    }
}
?>
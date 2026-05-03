<?php
session_start();
require 'db_connect.php';

if (isset($_POST['add_course_btn'])) {
    
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $level = $_POST['level'];

    // Handle Image Upload
    $target_dir = "uploads/courses/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

    $file_name = time() . "_" . $_FILES['thumbnail']['name'];
    $target_file = $target_dir . basename($file_name);
    
    if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $target_file)) {
        
        // Insert into Database
        $sql = "INSERT INTO courses (title, description, level, thumbnail) 
                VALUES ('$title', '$desc', '$level', '$target_file')";

        if ($conn->query($sql)) {
            echo "<script>alert('Course Added Successfully!'); window.location.href='admin_manage_courses.php';</script>";
        } else {
            echo "Error: " . $conn->error;
        }

    } else {
        echo "Error uploading image.";
    }
}
?>
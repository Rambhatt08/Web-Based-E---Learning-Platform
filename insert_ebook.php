<?php
session_start();
require 'db_connect.php';

if (isset($_POST['upload_btn'])) {
    
    // 1. Get Text Info
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $branch = $_POST['branch'];
    $year = $_POST['year_level'];

    // 2. Handle Main File (PDF/ZIP)
    $file_name = time() . "_" . $_FILES['ebook_file']['name'];
    $target_dir = "uploads/ebooks/";
    
    // Auto-create folder if missing
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
    
    $target_file = $target_dir . basename($file_name);
    move_uploaded_file($_FILES['ebook_file']['tmp_name'], $target_file);

    // 3. Handle Cover Image
    $thumb_path = "";
    if (!empty($_FILES['thumbnail']['name'])) {
        $thumb_name = time() . "_cover_" . $_FILES['thumbnail']['name'];
        $thumb_dir = "uploads/thumbnails/";
        
        // Auto-create folder if missing
        if (!is_dir($thumb_dir)) { mkdir($thumb_dir, 0777, true); }
        
        $thumb_path = $thumb_dir . basename($thumb_name);
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumb_path);
    }

    // 4. Save to Database
    $sql = "INSERT INTO ebooks (title, branch, year_level, file_path, cover_image) 
            VALUES ('$title', '$branch', '$year', '$target_file', '$thumb_path')";

    if ($conn->query($sql)) {
        // SUCCESS: Go back to Step 1 (The List)
        echo "<script>alert('Uploaded Successfully!'); window.location.href='admin_manage_ebooks.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
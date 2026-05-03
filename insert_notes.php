<?php
// insert_notes.php
session_start();

// 1. Database Connection
// We use the central connection file to avoid "Access Denied" errors
require 'db_connect.php';

// SECURITY: Ensure only Admin can upload (Optional but recommended)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // If you don't have role checks yet, you can comment this out
    // header("Location: login.php");
    // exit();
}

// 2. Check if button is clicked
if (isset($_POST['upload_btn'])) {

    // --- GET TEXT DATA & SECURE IT ---
    $title   = mysqli_real_escape_string($conn, $_POST['title']);
    $branch  = mysqli_real_escape_string($conn, $_POST['branch']);
    $year    = mysqli_real_escape_string($conn, $_POST['year_level']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);

    // --- CREATE DIRECTORIES IF THEY DON'T EXIST ---
    if (!is_dir('uploads/notes')) {
        mkdir('uploads/notes', 0777, true);
    }
    if (!is_dir('uploads/thumbnails')) {
        mkdir('uploads/thumbnails', 0777, true);
    }

    // --- HANDLE PDF UPLOAD ---
    $pdf_name = time() . "_" . $_FILES['note_file']['name']; // Adding timestamp to prevent overwriting
    $pdf_tmp = $_FILES['note_file']['tmp_name'];
    $pdf_destination = "uploads/notes/" . $pdf_name; 

    move_uploaded_file($pdf_tmp, $pdf_destination);

    // --- HANDLE THUMBNAIL UPLOAD ---
    $thumb_name = $_FILES['thumbnail']['name'];
    $thumb_destination = ""; 

    if($thumb_name != "") {
        $thumb_name = time() . "_" . $thumb_name; // Adding timestamp
        $thumb_tmp = $_FILES['thumbnail']['tmp_name'];
        $thumb_destination = "uploads/thumbnails/" . $thumb_name;
        move_uploaded_file($thumb_tmp, $thumb_destination);
    } else {
        // Set a default image path if they didn't upload one
        $thumb_destination = "uploads/thumbnails/default_note.png"; 
    }

    // --- INSERT INTO DATABASE ---
    // Using $conn (from db_connect.php) which is a mysqli object
    $sql = "INSERT INTO notes (title, branch, year_level, subject, file_path, thumbnail) 
            VALUES ('$title', '$branch', '$year', '$subject', '$pdf_destination', '$thumb_destination')";

    if ($conn->query($sql)) {
        echo "<script>
                alert('Notes uploaded successfully!');
                window.location.href = 'admin_manage_notes.php';
              </script>";
    } else {
        echo "Database Error: " . $conn->error;
    }

} else {
    header("Location: admin_manage_notes.php");
    exit();
}
?>
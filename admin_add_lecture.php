<?php
session_start();
require 'db_connect.php';

// 1. Check ID
if (!isset($_GET['course_id'])) { 
    header("Location: admin_manage_courses.php"); 
    exit(); 
}
$course_id = intval($_GET['course_id']);

// 2. Handle Form Submit
if (isset($_POST['add_btn'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    $order = intval($_POST['order']);
    $type = $_POST['video_type'];
    
    $video_url = "";

    if ($type == 'youtube') {
        // Convert regular YouTube link to Embed link
        $raw_url = $_POST['yt_link'];
        if (strpos($raw_url, 'watch?v=') !== false) {
            $video_url = str_replace("watch?v=", "embed/", $raw_url);
            $video_url = explode("&", $video_url)[0];
        } else {
            $video_url = $raw_url; 
        }
    } else {
        // Handle File Upload (MP4)
        // 1. Clean the filename to prevent broken URLs (removes spaces/special characters)
        $original_name = basename($_FILES['video_file']['name']);
        $clean_name = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $original_name);
        
        // 2. Define the path (ensure it matches your folder structure)
        $target_dir = "uploads/videos/";
        $target_file = $target_dir . $clean_name;
        
        // 3. Ensure the directory exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // 4. Move the file and set the URL for database
        if (move_uploaded_file($_FILES['video_file']['tmp_name'], $target_file)) {
            $video_url = $target_file; // Saves as: uploads/videos/12345_video.mp4
        } else {
            // Error handling if upload fails
            $error_code = $_FILES['video_file']['error'];
            die("Upload failed. Error code: " . $error_code . ". Tip: Check your php.ini upload limits.");
        }
    }

    // Insert into Database
    $sql = "INSERT INTO course_lectures (course_id, title, video_url, duration, lecture_order) 
            VALUES ($course_id, '$title', '$video_url', '$duration', $order)";
    
    if ($conn->query($sql)) {
        header("Location: admin_manage_lectures.php?course_id=$course_id");
        exit();
    } else {
        echo "Database Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Lecture - Smart Learn</title>
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; margin-bottom: 20px; }
        label { font-weight: 600; display: block; margin-top: 15px; color: #555; }
        input, select { width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: 600; transition: 0.3s; }
        button:hover { background: #219150; }
        .cancel-link { display: block; text-align: center; margin-top: 15px; color: #7f8c8d; text-decoration: none; }
    </style>
    
    <script>
        function toggleInput() {
            var type = document.getElementById('v_type').value;
            if(type == 'youtube') {
                document.getElementById('yt_input').style.display = 'block';
                document.getElementById('file_input').style.display = 'none';
            } else {
                document.getElementById('yt_input').style.display = 'none';
                document.getElementById('file_input').style.display = 'block';
            }
        }
    </script>
</head>
<body>

<div class="container">
    <h2 style="text-align:center;">Add New Topic</h2>
    
    <form method="POST" enctype="multipart/form-data">
        <label>Topic Title</label>
        <input type="text" name="title" required placeholder="e.g. Introduction to Python">
        
        <label>Duration (e.g. 10:30)</label>
        <input type="text" name="duration" required placeholder="10:00">
        
        <label>Order Number</label>
        <input type="number" name="order" value="1" required>

        <label>Video Source</label>
        <select name="video_type" id="v_type" onchange="toggleInput()">
            <option value="youtube">YouTube Link</option>
            <option value="file">Upload MP4 File</option>
        </select>

        <div id="yt_input">
            <label>YouTube URL</label>
            <input type="text" name="yt_link" placeholder="Paste YouTube Link Here">
        </div>

        <div id="file_input" style="display:none;">
            <label>Select Video File (MP4)</label>
            <input type="file" name="video_file" accept="video/mp4">
            <p style="font-size: 12px; color: #7f8c8d; margin-top: 5px;">
    <i class="fas fa-info-circle"></i> <strong>Admin Note:</strong> High-quality MP4 files up to 800MB are supported For best performance.
</p>
        </div>

        <button type="submit" name="add_btn">Add Topic</button>
    </form>
    
    <a href="admin_manage_lectures.php?course_id=<?php echo $course_id; ?>" class="cancel-link">Cancel</a>
</div>

</body>
</html>
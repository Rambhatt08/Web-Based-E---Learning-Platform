<?php 
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Course</title>
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        input, select, textarea { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin-top: 10px;}
        button:hover { background: #2980b9; }
    </style>
</head>
<body>

<div class="container">
    <h2 style="text-align:center;">Add New Course</h2>
    
    <form action="insert_course.php" method="POST" enctype="multipart/form-data">
        
        <label>Course Title</label>
        <input type="text" name="title" required placeholder="e.g. Master React JS">
        
        <label>Description</label>
        <textarea name="description" rows="4" required placeholder="What will students learn?"></textarea>
        
        <label>Difficulty Level</label>
        <select name="level">
            <option value="Beginner">Beginner</option>
            <option value="Intermediate">Intermediate</option>
            <option value="Advanced">Advanced</option>
        </select>

        <label>Course Thumbnail (Image)</label>
        <input type="file" name="thumbnail" accept="image/*" required>
        
        <button type="submit" name="add_course_btn">Create Course</button>
    </form>
    
    <a href="admin_manage_courses.php" style="display:block; text-align:center; margin-top:15px; color:#555; text-decoration:none;">Cancel</a>
</div>

</body>
</html>
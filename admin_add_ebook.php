<?php 
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload E-Book</title>
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        input, select { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; }
        button { width: 100%; padding: 12px; background: #8e44ad; color: white; border: none; cursor: pointer; border-radius: 4px; margin-top: 15px; }
        button:hover { background: #732d91; }
    </style>
</head>
<body>

<div class="container">
    <h2>Upload New E-Book</h2>
    
    <form action="insert_ebook.php" method="POST" enctype="multipart/form-data">
        
        <label>Title</label>
        <input type="text" name="title" required placeholder="e.g. Java Programming">
        
        <label>Branch</label> 
        <select name="branch" required>
            <option value="">-- Select Branch --</option>
            <option value="IT">Information Technology</option>
            <option value="CE">Computer Engineering</option>
        </select>
        
        <label>Year</label>
        <select name="year_level" required>
            <option value="">-- Select Year --</option>
            <option value="1st Year">1st Year</option>
            <option value="2nd Year">2nd Year</option>
            <option value="3rd Year">3rd Year</option>
            <option value="4th Year">4th Year</option>
        </select>

        <label>Cover Image</label>
        <input type="file" name="thumbnail" accept="image/*">
        
        <label>File (PDF/ZIP)</label>
        <input type="file" name="ebook_file" required>
        
        <button type="submit" name="upload_btn">Upload</button>
    </form>
    
    <a href="admin_manage_ebooks.php" style="display:block; text-align:center; margin-top:15px; color:#777; text-decoration:none;">Cancel</a>
</div>

</body>
</html>
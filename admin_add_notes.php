<?php 
session_start();
// Include your existing database connection if you have a separate file, 
// otherwise we connect in the next script.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Notes</title>
    <style>
        /* Basic Styling to make it look clean */
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; padding: 10px; margin-bottom: 5px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #218838; }
        .btn-back { display: block; text-align: center; margin-top: 15px; text-decoration: none; color: #555; }
    </style>
</head>
<body>

<div class="container">
    <h2>Add New Notes</h2>
    
    <form action="insert_notes.php" method="POST" enctype="multipart/form-data">
        
        <div class="form-group">
            <label>Note Title</label>
            <input type="text" name="title" required placeholder="e.g. Data Structures Unit 1">
        </div>

        <div class="form-group">
            <label>Branch</label>
            <select name="branch" required>
                <option value="">Select Branch</option>
                <option value="IT">Information Technology (IT)</option>
                <option value="CE">Computer Engineering (CE)</option>
            </select>
        </div>

        <div class="form-group">
            <label>Year Level</label>
            <select name="year_level" required>
                <option value="">Select Year</option>
                <option value="1st Year">1st Year</option>
                <option value="2nd Year">2nd Year</option>
                <option value="3rd Year">3rd Year</option>
                <option value="4th Year">4th Year</option>
            </select>
        </div>

        <div class="form-group">
            <label>Subject</label>
            <input type="text" name="subject" required placeholder="e.g. Mathematics II">
        </div>

        <div class="form-group">
            <label>Cover Image (Thumbnail)</label>
            <input type="file" name="thumbnail" accept="image/*">
        </div>

        <div class="form-group">
            <label>Notes PDF File</label>
            <input type="file" name="note_file" accept=".pdf" required>
        </div>

        <button type="submit" name="upload_btn">Upload Notes</button>
    </form>

    <a href="admin-dashboard.php" class="btn-back">Back to Dashboard</a>
</div>

</body>
</html>
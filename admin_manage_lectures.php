<?php
session_start();
require 'db_connect.php';

// 1. Check ID
if (!isset($_GET['course_id'])) {
    header("Location: admin_manage_courses.php");
    exit();
}
$course_id = intval($_GET['course_id']);

// 2. Fetch Course Info (For the title)
$course_q = $conn->query("SELECT title FROM courses WHERE id = $course_id");
$course = $course_q->fetch_assoc();

// 3. Handle Deletion
if (isset($_GET['delete_video'])) {
    $vid_id = intval($_GET['delete_video']);
    $conn->query("DELETE FROM course_lectures WHERE id = $vid_id");
    header("Location: admin_manage_lectures.php?course_id=$course_id");
    exit();
}

// 4. Fetch Videos
$sql = "SELECT * FROM course_lectures WHERE course_id = $course_id ORDER BY lecture_order ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Lectures</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 20px; }
        .btn-add { background: #8e44ad; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #f8f9fa; }
        .btn-del { color: #e74c3c; cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div>
            <h2 style="margin:0;">Course Content</h2>
            <small style="color:#777;">For: <?php echo htmlspecialchars($course['title']); ?></small>
        </div>
        <a href="admin_add_lecture.php?course_id=<?php echo $course_id; ?>" class="btn-add">+ Add Video Topic</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Topic Title</th>
                <th>Duration</th>
                <th>Type</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['lecture_order']; ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo $row['duration']; ?></td>
                        <td>
                            <?php echo (strpos($row['video_url'], 'youtube') !== false) ? '<i class="fab fa-youtube" style="color:red;"></i> YouTube' : '<i class="fas fa-file-video" style="color:blue;"></i> File'; ?>
                        </td>
                        <td>
                            <a href="admin_manage_lectures.php?course_id=<?php echo $course_id; ?>&delete_video=<?php echo $row['id']; ?>" 
                               class="btn-del" onclick="return confirm('Delete this topic?');">
                               <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center; padding:20px;">No videos added yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <a href="admin_manage_courses.php" style="display:block; margin-top:20px; text-decoration:none; color:#555;">&larr; Back to Courses</a>
</div>

</body>
</html>
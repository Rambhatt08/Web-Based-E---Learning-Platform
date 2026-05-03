<?php
session_start();
require 'db_connect.php';

// 1. Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 2. Handle Deletion
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    
    // Get thumbnail path to delete the image file
    $query = $conn->query("SELECT thumbnail FROM courses WHERE id = $id");
    $row = $query->fetch_assoc();
    
    // Delete from Database
    $sql = "DELETE FROM courses WHERE id = $id";
    if ($conn->query($sql)) {
        // Delete the image file if it exists
        if (!empty($row['thumbnail']) && file_exists($row['thumbnail'])) {
            unlink($row['thumbnail']);
        }
        header("Location: admin_manage_courses.php"); 
        exit();
    }
}

// 3. Fetch All Courses
$sql = "SELECT * FROM courses ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Courses - Smart Learn</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        
        /* Header */
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #f4f4f4; padding-bottom: 15px; }
        h2 { margin: 0; color: #333; }
        
        /* Add Button */
        .btn-add { background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .btn-add:hover { background: #218838; }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #ddd; vertical-align: middle; }
        th { background-color: #f8f9fa; color: #333; font-weight: 600; }
        
        .thumb-img { width: 80px; height: 50px; object-fit: cover; border-radius: 4px; }
        .btn-delete { color: #dc3545; font-size: 18px; margin-left: 10px; }
        .back-link { display: inline-block; margin-top: 20px; text-decoration: none; color: #777; }
        
        /* Badge for Level */
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; color: white; }
        .bg-beg { background: #2ecc71; }
        .bg-int { background: #f39c12; }
        .bg-adv { background: #e74c3c; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>Manage Courses</h2>
        <a href="admin_add_course.php" class="btn-add">+ Add New Course</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Thumbnail</th>
                <th>Title</th>
                <th>Level</th>
                <th>Enrolled</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td>
                            <?php $thumb = !empty($row['thumbnail']) ? $row['thumbnail'] : 'imgs/default.jpg'; ?>
                            <img src="<?php echo $thumb; ?>" class="thumb-img">
                        </td>
                        <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                        <td>
                            <?php 
                                $lvl = $row['level'];
                                $cls = ($lvl=='Beginner')?'bg-beg':(($lvl=='Intermediate')?'bg-int':'bg-adv');
                            ?>
                            <span class="badge <?php echo $cls; ?>"><?php echo $lvl; ?></span>
                        </td>
                        <td><?php echo $row['total_enrolled']; ?> Students</td>
                        <td>
                            <a href="admin_manage_lectures.php?course_id=<?php echo $row['id']; ?>" 
                               style="color:#3498db; margin-right:10px; font-size:18px;" 
                               title="Manage Videos">
                               <i class="fas fa-file-video"></i>
                            </a>

                            <a href="admin_manage_courses.php?delete_id=<?php echo $row['id']; ?>" 
                               class="btn-delete" 
                               onclick="return confirm('Delete this Course?');">
                               <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align:center; padding:30px;">No courses found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="admin-dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
</div>

</body>
</html>
<?php
session_start();
require 'db_connect.php';

// 1. Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 2. Handle Deletion (Un-enroll a student manually)
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM enrollments WHERE id = $id");
    header("Location: admin_enrollments.php");
    exit();
}

// 3. Fetch Enrollment Data
// FIX: Using 'u.User_Name' to match your database exactly
$sql = "SELECT 
            e.id AS enrollment_id,
            u.User_Name AS student_name, 
            u.email AS student_email,
            c.title AS course_title,
            e.enrolled_at
        FROM enrollments e
        JOIN users u ON e.user_id = u.id
        JOIN courses c ON e.course_id = c.id
        ORDER BY e.enrolled_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enrolled History - Smart Learn</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; padding: 20px; }
        .container { max-width: 1100px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f4f4f4; padding-bottom: 15px; margin-bottom: 20px; }
        h2 { margin: 0; color: #333; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #ddd; vertical-align: middle; }
        th { background-color: #f8f9fa; color: #333; font-weight: 600; }
        tr:hover { background-color: #f1f1f1; }
        
        .student-info { display: flex; flex-direction: column; }
        .email-text { font-size: 12px; color: #777; }
        
        .badge { background: #e3f2fd; color: #1976d2; padding: 5px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; }
        
        .btn-delete { color: #dc3545; font-size: 16px; margin-left: 10px; cursor: pointer; transition: 0.2s; }
        .btn-delete:hover { transform: scale(1.2); }

        .back-link { display: inline-block; margin-top: 20px; text-decoration: none; color: #777; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>Enrollment History</h2>
        <span>Total Enrollments: <strong><?php echo $result->num_rows; ?></strong></span>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Student Name</th>
                <th>Course Enrolled</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $row['enrollment_id']; ?></td>
                        
                        <td>
                            <div class="student-info">
                                <strong><?php echo htmlspecialchars($row['student_name']); ?></strong>
                                <span class="email-text"><?php echo htmlspecialchars($row['student_email']); ?></span>
                            </div>
                        </td>
                        
                        <td>
                            <span class="badge"><?php echo htmlspecialchars($row['course_title']); ?></span>
                        </td>
                        
                        <td><?php echo date("d M Y, h:i A", strtotime($row['enrolled_at'])); ?></td>
                        
                        <td>
                            <a href="admin_enrollments.php?delete_id=<?php echo $row['enrollment_id']; ?>" 
                               class="btn-delete" 
                               onclick="return confirm('Remove student from course?');"
                               title="Un-enroll Student">
                               <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center; padding:30px; color:#777;">No enrollments found yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="admin-dashboard.php" class="back-link">&larr; Back to Dashboard</a>
</div>

</body>
</html>
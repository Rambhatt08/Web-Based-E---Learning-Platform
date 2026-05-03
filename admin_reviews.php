<?php
session_start();
require 'db_connect.php';

// 1. SECURITY: Strict check for Admin Role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 2. DELETE REVIEW LOGIC
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $del_sql = "DELETE FROM reviews WHERE id = $id";
    if ($conn->query($del_sql)) {
        echo "<script>alert('Review deleted successfully!'); window.location.href='admin_reviews.php';</script>";
    } else {
        echo "<script>alert('Error deleting review.'); window.location.href='admin_reviews.php';</script>";
    }
}

// 3. FETCH ALL REVIEWS (Joined with Users and Courses)
$sql = "SELECT r.*, u.User_Name, c.title AS course_title 
        FROM reviews r 
        JOIN users u ON r.user_id = u.id 
        JOIN courses c ON r.course_id = c.id 
        ORDER BY r.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Reviews - Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* ADMIN LAYOUT (Matches Dashboard) */
        body { display: flex; background: #f4f7f6; margin: 0; min-height: 100vh; font-family: 'Poppins', sans-serif; }
        
        /* SIDEBAR */
        .sidebar { width: 260px; background: #2c3e50; color: white; display: flex; flex-direction: column; position: fixed; height: 100%; }
        .sidebar h2 { text-align: center; padding: 20px 0; background: #1a252f; margin: 0; }
        .sidebar a { padding: 15px 20px; color: #b8c7ce; text-decoration: none; border-left: 3px solid transparent; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: #34495e; color: white; border-left-color: #3498db; }
        .sidebar i { margin-right: 10px; width: 20px; }

        /* CONTENT */
        .main-content { flex: 1; margin-left: 260px; padding: 30px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }

        /* REVIEWS TABLE */
        .table-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 800px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f8f9fa; color: #555; font-weight: 600; }
        tr:hover { background-color: #f9f9f9; }
        
        /* RATING STARS */
        .star-rating { color: #f1c40f; font-size: 13px; }
        
        /* BUTTONS */
        .btn-delete {
            background: #e74c3c; color: white; border: none; padding: 6px 12px;
            border-radius: 4px; cursor: pointer; font-size: 12px; text-decoration: none;
            display: inline-flex; align-items: center; gap: 5px; transition: 0.2s;
        }
        .btn-delete:hover { background: #c0392b; }

        .empty-state { text-align: center; padding: 40px; color: #888; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Smart Learn</h2>
        <a href="admin-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="admin_manage_courses.php"><i class="fas fa-video"></i> Manage Courses</a>
        <a href="admin_manage_notes.php"><i class="fas fa-file-pdf"></i> Manage Notes</a>
        <a href="admin_manage_ebooks.php"><i class="fas fa-book"></i> Manage E-Books</a>
        <a href="admin_students.php"><i class="fas fa-users"></i> Registered Students</a>
        <a href="admin_enrollments.php"><i class="fas fa-graduation-cap"></i> Enrolled History</a>
        
        <a href="admin_reviews.php" class="active"><i class="fas fa-star"></i> Manage Reviews</a>
        
        <a href="admin_queries.php"><i class="fas fa-envelope"></i> Queries & Subs</a>
        <a href="admin_manage_quizzes.php"><i class="fas fa-question-circle"></i> Manage Quizzes</a>
        <a href="admin_quiz_results.php"><i class="fas fa-chart-line"></i> Quiz Results</a>
        <a href="logout.php" style="margin-top: auto; background: #c0392b;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Manage Reviews</h1>
            <span>admin</span>
        </div>

        <div class="table-container">
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Rating</th>
                            <th style="width: 35%;">Review</th> <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['User_Name']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($row['course_title']); ?></td>
                            <td>
                                <div class="star-rating">
                                    <?php 
                                    // Logic to show stars dynamically
                                    for($i=1; $i<=5; $i++) {
                                        echo ($i <= $row['rating']) ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                                    }
                                    ?>
                                </div>
                            </td>
                            <td>
                                <em style="color:#555;">"<?php echo htmlspecialchars($row['comment']); ?>"</em>
                            </td>
                            <td><?php echo date("M d, Y", strtotime($row['created_at'])); ?></td>
                            <td>
                                <a href="admin_reviews.php?delete=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this review?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="far fa-folder-open" style="font-size: 40px; margin-bottom: 10px; opacity: 0.5;"></i>
                    <p>No reviews have been submitted yet.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>

</body>
</html>
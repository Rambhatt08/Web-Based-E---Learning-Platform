<?php
session_start();
require 'db_connect.php'; // Adjust path if needed

// SECURITY: Strict check for Admin Role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// --- NEW: DELETE ATTEMPT LOGIC ---
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // 1. Delete detailed responses first (to keep DB clean and avoid constraint errors)
    $conn->query("DELETE FROM quiz_responses WHERE attempt_id = $delete_id");
    
    // 2. Delete the main attempt
    if ($conn->query("DELETE FROM quiz_attempts WHERE id = $delete_id")) {
        header("Location: admin_quiz_results.php?msg=deleted");
        exit();
    }
}

// FETCH ALL QUIZ ATTEMPTS (Join with users, quizzes, and courses)
$sql = "SELECT qa.*, u.User_Name, q.title AS quiz_title, c.title AS course_title 
        FROM quiz_attempts qa 
        JOIN users u ON qa.user_id = u.id 
        JOIN quizzes q ON qa.quiz_id = q.id 
        JOIN courses c ON q.course_id = c.id 
        ORDER BY qa.attempted_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Quiz Results - Admin</title>
    <link rel="stylesheet" href="style.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* ADMIN LAYOUT */
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

        /* TABLE STYLING */
        .table-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f8f9fa; color: #333; font-weight: 600; }
        tr:hover { background-color: #f9f9f9; }
        
        .score-badge { 
            padding: 5px 10px; border-radius: 20px; font-weight: bold; font-size: 14px;
        }
        .score-good { background: #d4edda; color: #155724; }
        .score-avg { background: #fff3cd; color: #856404; }
        .score-poor { background: #f8d7da; color: #721c24; }

        /* Button Styling */
        .btn-view {
            background: #3498db;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: 0.2s;
            margin-right: 5px;
            display: inline-block;
        }
        .btn-view:hover { background: #2980b9; }

        /* NEW: Delete Button Styling */
        .btn-delete {
            background: #e74c3c;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            transition: 0.2s;
            display: inline-block;
        }
        .btn-delete:hover { background: #c0392b; }

        .msg-success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb; }

        .empty-state { text-align: center; padding: 50px; color: #888; }
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
        <a href="admin_reviews.php"><i class="fas fa-star"></i> Manage Reviews</a>
        <a href="admin_queries.php"><i class="fas fa-envelope"></i> Queries & Subs</a>
        <a href="admin_manage_quizzes.php"><i class="fas fa-question-circle"></i> Manage Quizzes</a>
        <a href="admin_quiz_results.php" class="active"><i class="fas fa-chart-line"></i> Quiz Results</a>
        <a href="admin_manage_qa.php"><i class="fas fa-comments"></i> Student Q&A</a>

        <a href="logout.php" style="margin-top: auto; background: #c0392b;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Student Quiz Results</h1>
            <span>Admin Panel</span>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div class="msg-success"><i class="fas fa-check-circle"></i> Attempt deleted successfully.</div>
        <?php endif; ?>

        <div class="table-container">
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Attempt ID</th>
                            <th>Student Name</th>
                            <th>Course / Subject</th>
                            <th>Quiz Title</th>
                            <th>Score</th>
                            <th>Date & Time</th>
                            <th>Action</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): 
                            // Calculate percentage for coloring
                            $percentage = ($row['score'] / $row['total_questions']) * 100;
                            if($percentage >= 70) $score_class = 'score-good';
                            elseif($percentage >= 40) $score_class = 'score-avg';
                            else $score_class = 'score-poor';
                        ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['User_Name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['course_title']); ?></td>
                            <td><?php echo htmlspecialchars($row['quiz_title']); ?></td>
                            <td>
                                <span class="score-badge <?php echo $score_class; ?>">
                                    <?php echo $row['score']; ?> / <?php echo $row['total_questions']; ?>
                                </span>
                            </td>
                            <td>
                                <i class="far fa-calendar-alt"></i> <?php echo date("d M Y", strtotime($row['attempted_at'])); ?><br>
                                <i class="far fa-clock"></i> <small><?php echo date("h:i A", strtotime($row['attempted_at'])); ?></small>
                            </td>
                            <td>
                                <a href="admin_view_quiz_details.php?attempt_id=<?php echo $row['id']; ?>" class="btn-view">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="admin_quiz_results.php?delete_id=<?php echo $row['id']; ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Are you sure you want to delete this result permanently?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-clipboard-list" style="font-size: 50px; color:#ccc; margin-bottom:15px;"></i>
                    <p>No students have attempted any quizzes yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
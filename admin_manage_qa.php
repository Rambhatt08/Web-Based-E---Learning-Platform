<?php
session_start();
require 'db_connect.php';

// 1. SECURITY: Strict check for Admin Role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";

// --- NEW: HANDLE DELETE QUESTION (Admin Side) ---
if (isset($_GET['delete_qa'])) {
    $qa_id = intval($_GET['delete_qa']);
    
    // Admin has power to delete any QA ID
    $sql_del = "DELETE FROM course_qa WHERE id = $qa_id";
    if ($conn->query($sql_del)) {
        echo "<script>alert('Question deleted successfully!'); window.location.href='admin_manage_qa.php';</script>";
        exit();
    }
}

// 2. HANDLE ADMIN REPLY SUBMISSION
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_reply'])) {
    $qa_id = intval($_POST['qa_id']);
    $reply = $conn->real_escape_string($_POST['admin_reply']);

    if (!empty($reply)) {
        $sql = "UPDATE course_qa SET 
                admin_reply = '$reply', 
                status = 'answered', 
                replied_at = NOW() 
                WHERE id = $qa_id";
        
        if ($conn->query($sql)) {
            $message = "Reply sent successfully!";
        }
    }
}

// 3. FETCH ALL QUESTIONS (Join with Users and Courses)
$sql_qa = "SELECT qa.*, u.User_Name, c.title as course_title 
           FROM course_qa qa 
           JOIN users u ON qa.user_id = u.id 
           JOIN courses c ON qa.course_id = c.id 
           ORDER BY qa.status ASC, qa.created_at DESC";
$qa_res = $conn->query($sql_qa);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Student Q&A - Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body { display: flex; background: #f4f7f6; margin: 0; min-height: 100vh; font-family: 'Poppins', sans-serif; }
        .sidebar { width: 260px; background: #2c3e50; color: white; display: flex; flex-direction: column; position: fixed; height: 100%; }
        .sidebar h2 { text-align: center; padding: 20px 0; background: #1a252f; margin: 0; }
        .sidebar a { padding: 15px 20px; color: #b8c7ce; text-decoration: none; border-left: 3px solid transparent; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: #34495e; color: white; border-left-color: #3498db; }
        .sidebar i { margin-right: 10px; width: 20px; }

        .main-content { flex: 1; margin-left: 260px; padding: 30px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }

        .qa-admin-card { background: white; padding: 25px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #ddd; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: relative; }
        .qa-meta { font-size: 13px; color: #777; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #eee; }
        
        /* NEW: TRASH ICON STYLING */
        .btn-delete-admin { color: #e74c3c; float: right; margin-left: 15px; font-size: 18px; transition: 0.2s; }
        .btn-delete-admin:hover { color: #c0392b; transform: scale(1.1); }

        .student-q { font-size: 16px; color: #333; margin: 15px 0; line-height: 1.6; }
        .reply-form textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Poppins'; margin-bottom: 10px; box-sizing: border-box; resize: vertical; }
        .btn-reply { background: #2ecc71; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: 600; }
        
        .status-pill { font-size: 11px; padding: 4px 10px; border-radius: 12px; float: right; font-weight: 700; text-transform: uppercase; }
        .pill-pending { background: #fff3cd; color: #856404; }
        .pill-answered { background: #d4edda; color: #155724; }

        .alert { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Smart Learn</h2>
        <a href="admin-dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="admin_manage_courses.php"><i class="fas fa-video"></i> Manage Courses</a>
        <a href="admin_manage_notes.php"><i class="fas fa-file-pdf"></i> Manage Notes</a>
        <a href="admin_manage_ebooks.php"><i class="fas fa-book"></i> Manage E-Books</a>
        <a href="admin_students.php"><i class="fas fa-users"></i> Registered Students</a>
        <a href="admin_enrollments.php"><i class="fas fa-graduation-cap"></i> Enrolled History</a>
        
        <a href="admin_reviews.php"><i class="fas fa-star"></i> Manage Reviews</a>
        
        <a href="admin_queries.php"><i class="fas fa-envelope"></i> Queries & Subs</a>
        <a href="admin_manage_quizzes.php"><i class="fas fa-question-circle"></i> Manage Quizzes</a>
        <a href="admin_quiz_results.php"><i class="fas fa-chart-line"></i> Quiz Results</a>
        <a href="admin_manage_qa.php"><i class="fas fa-comments"></i> Student Q&A</a>

        <a href="logout.php" style="margin-top: auto; background: #c0392b;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Student Questions & Doubts</h1>
            <span>Welcome, Admin</span>
        </div>

        <?php if($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if ($qa_res->num_rows > 0): ?>
            <?php while($row = $qa_res->fetch_assoc()): ?>
                <div class="qa-admin-card">
                    <a href="admin_manage_qa.php?delete_qa=<?php echo $row['id']; ?>" 
                       class="btn-delete-admin" 
                       onclick="return confirm('Delete this question permanently?')" 
                       title="Delete Question">
                       <i class="fas fa-trash-alt"></i>
                    </a>

                    <span class="status-pill <?php echo ($row['status'] == 'answered') ? 'pill-answered' : 'pill-pending'; ?>">
                        <?php echo $row['status']; ?>
                    </span>
                    <div class="qa-meta">
                        <strong>Student:</strong> <?php echo htmlspecialchars($row['User_Name']); ?> | 
                        <strong>Course:</strong> <?php echo htmlspecialchars($row['course_title']); ?> | 
                        <strong>Date:</strong> <?php echo date('M d, Y h:i A', strtotime($row['created_at'])); ?>
                    </div>
                    
                    <div class="student-q">
                        <strong>Question:</strong><br>
                        <?php echo nl2br(htmlspecialchars($row['question_text'])); ?>
                    </div>

                    <form action="admin_manage_qa.php" method="POST" class="reply-form">
                        <input type="hidden" name="qa_id" value="<?php echo $row['id']; ?>">
                        <textarea name="admin_reply" rows="3" placeholder="Type your answer here..." required><?php echo htmlspecialchars($row['admin_reply']); ?></textarea>
                        <button type="submit" name="submit_reply" class="btn-reply">
                            <?php echo ($row['status'] == 'answered') ? 'Update Reply' : 'Send Answer'; ?>
                        </button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align:center; padding: 50px; background:white; border-radius:8px; color:#888;">
                <i class="fas fa-check-circle" style="font-size: 50px; margin-bottom:15px; opacity:0.3;"></i>
                <p>No student questions found.</p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
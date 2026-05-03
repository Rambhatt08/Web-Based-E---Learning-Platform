<?php
session_start();
require 'db_connect.php';

// SECURITY: Strict check for Admin Role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['user_name']; // Use session name

// --- FETCH QUICK STATS ---

// 1. Total Students
$res1 = $conn->query("SELECT count(*) as total FROM users WHERE role='student'");
$student_count = $res1->fetch_assoc()['total'];

// 2. Total Courses
$res2 = $conn->query("SELECT count(*) as total FROM courses");
$course_count = $res2->fetch_assoc()['total'];

// 3. Total Notes
$res3 = $conn->query("SELECT count(*) as total FROM notes");
$note_count = $res3->fetch_assoc()['total'];

// 4. Total E-Books
$res4 = $conn->query("SELECT count(*) as total FROM ebooks");
$ebook_count = $res4->fetch_assoc()['total'];

// 5. Total Contact Messages
$res5 = $conn->query("SELECT count(*) as total FROM contact_messages");
$msg_count = $res5->fetch_assoc()['total'];

// 6. Total Subscribers
$res6 = $conn->query("SELECT count(*) as total FROM newsletter");
$sub_count = $res6->fetch_assoc()['total'];

// 7. Total Reviews
$res7 = $conn->query("SELECT count(*) as total FROM reviews");
$review_count = $res7->fetch_assoc()['total'];

// 8. Total Quiz Results (New)
$res8 = $conn->query("SELECT count(*) as total FROM quiz_attempts");
$quiz_results_count = $res8->fetch_assoc()['total'];

// 9. Total Student Q&A (New)
$res9 = $conn->query("SELECT count(*) as total FROM course_qa");
$qa_count = $res9->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Smart Learn</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* ADMIN SPECIFIC CSS */
        body { display: flex; background: #f4f7f6; margin: 0; min-height: 100vh; font-family: 'Poppins', sans-serif; }
        
        /* SIDEBAR */
        .sidebar { width: 260px; background: #2c3e50; color: white; display: flex; flex-direction: column; position: fixed; height: 100%; }
        .sidebar h2 { text-align: center; padding: 20px 0; background: #1a252f; margin: 0; }
        .sidebar a { padding: 15px 20px; color: #b8c7ce; text-decoration: none; border-left: 3px solid transparent; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: #34495e; color: white; border-left-color: #3498db; }
        .sidebar i { margin-right: 10px; width: 20px; }

        /* MAIN CONTENT */
        .main-content { flex: 1; margin-left: 260px; padding: 30px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        /* STAT CARDS */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; align-items: center; }
        .card-icon { width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-right: 15px; color: white; }
        .card-info h3 { margin: 0; font-size: 28px; }
        .card-info p { margin: 0; color: #777; font-size: 14px; }

        /* Colors */
        .blue { background: #3498db; }
        .green { background: #2ecc71; }
        .orange { background: #f39c12; }
        .red { background: #e74c3c; }
        .purple { background: #9b59b6; }
        .pink { background: #e84393; } /* For Quiz Results */
        .teal { background: #00cec9; } /* For Q&A */
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
            <h1>Welcome, Admin</h1>
            <span><?php echo htmlspecialchars($user_name); ?></span>
        </div>

        <div class="stats-grid">
            <div class="card">
                <div class="card-icon blue"><i class="fas fa-user-graduate"></i></div>
                <div class="card-info">
                    <h3><?php echo $student_count; ?></h3>
                    <p>Total Students</p>
                </div>
            </div>

            <div class="card">
                <div class="card-icon green"><i class="fas fa-laptop-code"></i></div>
                <div class="card-info">
                    <h3><?php echo $course_count; ?></h3>
                    <p>Courses Available</p>
                </div>
            </div>

            <div class="card">
                <div class="card-icon orange"><i class="fas fa-file-alt"></i></div>
                <div class="card-info">
                    <h3><?php echo $note_count; ?></h3>
                    <p>Notes Uploaded</p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-icon red"><i class="fas fa-book-open"></i></div>
                <div class="card-info">
                    <h3><?php echo $ebook_count; ?></h3> 
                    <p>E-Books</p>
                </div>
            </div>

            <div class="card">
                <div class="card-icon purple"><i class="fas fa-star"></i></div>
                <div class="card-info">
                    <h3><?php echo $review_count; ?></h3>
                    <p>Total Reviews</p>
                </div>
            </div>

            <div class="card">
                <div class="card-icon pink"><i class="fas fa-poll"></i></div>
                <div class="card-info">
                    <h3><?php echo $quiz_results_count; ?></h3>
                    <p>Quiz Results</p>
                </div>
            </div>

            <div class="card">
                <div class="card-icon teal"><i class="fas fa-question-circle"></i></div>
                <div class="card-info">
                    <h3><?php echo $qa_count; ?></h3>
                    <p>Student Q&A</p>
                </div>
            </div>

            <div class="card">
                <div class="card-icon blue"><i class="fas fa-comment-alt"></i></div>
                <div class="card-info">
                    <h3><?php echo $msg_count; ?></h3>
                    <p>User Queries</p>
                </div>
            </div>

            <div class="card">
                <div class="card-icon green"><i class="fas fa-paper-plane"></i></div>
                <div class="card-info">
                    <h3><?php echo $sub_count; ?></h3>
                    <p>Subscribers</p>
                </div>
            </div>

        </div>
        
        <div style="margin-top: 50px; text-align: center; color: #888;">
            <p>Select an option from the sidebar to manage content.</p>
        </div>

    </div>

</body>
</html>
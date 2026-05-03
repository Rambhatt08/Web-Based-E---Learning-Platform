<?php
session_start();
require 'db_connect.php';

// 1. SECURITY CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$message = "";

// --- NEW: 2. HANDLE DELETE QUESTION (Student Side) ---
if (isset($_GET['delete_qa'])) {
    $qa_id = intval($_GET['delete_qa']);
    // Security: only delete if the question belongs to the current student
    $sql_del = "DELETE FROM course_qa WHERE id = $qa_id AND user_id = $user_id";
    if ($conn->query($sql_del)) {
        header("Location: question-answer.php?msg=deleted");
        exit();
    }
}

// 2. HANDLE NEW QUESTION SUBMISSION
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_question'])) {
    $course_id = intval($_POST['course_id']);
    $question = $conn->real_escape_string($_POST['question_text']);

    if ($course_id > 0 && !empty($question)) {
        $sql = "INSERT INTO course_qa (user_id, course_id, question_text) VALUES ($user_id, $course_id, '$question')";
        if ($conn->query($sql)) {
            $message = "Your question has been sent to the instructor!";
        }
    }
}

// 3. FETCH ENROLLED COURSES (For the dropdown menu)
$courses_res = $conn->query("SELECT c.id, c.title FROM courses c JOIN enrollments e ON c.id = e.course_id WHERE e.user_id = $user_id");

// 4. FETCH Q&A HISTORY WITH FILTERING
$filter = isset($_GET['sort']) ? $_GET['sort'] : 'all';
$sql_qa = "SELECT qa.*, c.title as course_title FROM course_qa qa 
           JOIN courses c ON qa.course_id = c.id 
           WHERE qa.user_id = $user_id";

if ($filter == 'answered') $sql_qa .= " AND qa.status = 'answered'";
if ($filter == 'unanswered') $sql_qa .= " AND qa.status = 'pending'";

$sql_qa .= " ORDER BY qa.created_at DESC";
$qa_res = $conn->query($sql_qa);

// Get counts for the dropdown
$total_count = $conn->query("SELECT COUNT(*) as count FROM course_qa WHERE user_id = $user_id")->fetch_assoc()['count'];

// Set delete message if redirected
if(isset($_GET['msg']) && $_GET['msg'] == 'deleted') {
    $message = "Question deleted successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Question & Answer - Smart Learn</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="imgs/favicon.png" type="image/png">

    <style>
        /* Q&A SPECIFIC STYLES */
        .qa-header-controls { display: flex; align-items: center; gap: 15px; margin-bottom: 20px; font-size: 14px; color: #555; }
        .qa-sort-select { padding: 8px 15px; border: 1px solid #ddd; border-radius: 4px; outline: none; color: #555; font-family: 'Poppins', sans-serif; min-width: 100px; }

        /* FORM STYLING */
        .ask-form-container { background: white; padding: 25px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #e0e0e0; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 15px; font-family: 'Poppins'; box-sizing: border-box; }
        .btn-ask { background: #3b82f6; color: white; border: none; padding: 10px 25px; border-radius: 5px; cursor: pointer; font-weight: 600; }
        .btn-ask:hover { background: #2563eb; }

        /* Q&A CARDS */
        .qa-card { background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; border: 1px solid #eee; box-shadow: 0 2px 5px rgba(0,0,0,0.02); position: relative; }
        .qa-badge { font-size: 11px; padding: 3px 10px; border-radius: 12px; font-weight: 600; text-transform: uppercase; }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-answered { background: #d4edda; color: #155724; }
        
        .reply-box { background: #f8f9fa; padding: 15px; border-left: 4px solid #3b82f6; border-radius: 4px; margin-top: 15px; }

        /* DELETE BUTTON STYLING */
        .btn-delete-qa { color: #e74c3c; text-decoration: none; font-size: 14px; transition: 0.2s; padding: 5px; }
        .btn-delete-qa:hover { color: #c0392b; transform: scale(1.1); }

        /* EMPTY STATE BOX */
        .qa-empty-box { background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); height: 300px; display: flex; flex-direction: column; align-items: center; justify-content: center; border: 1px solid #eee; }
        .empty-icon { font-size: 60px; color: #e0e0e0; margin-bottom: 15px; }
        .empty-text { font-weight: 600; color: #333; font-size: 16px; }
    </style>
</head>
<body>

<nav class="navbar dashboard-nav">
    <div class="logo"><a href="index.html"><img src="imgs/logo.jpg" alt="Smart Learn Logo"></a></div>
    <ul class="nav-links">
        <li><a href="index.html">Home</a></li>
        <li><a href="courses.php">Course</a></li>
        <li class="dropdown-parent"><a href="#">Notes <i class="fas fa-chevron-down"></i></a>
            <ul class="dropdown-menu"><li><a href="#">IT / CE Notes...</a></li></ul> 
        </li>
        <li class="dropdown-parent"><a href="#">E-Book <i class="fas fa-chevron-down"></i></a>
            <ul class="dropdown-menu"><li><a href="#">IT / CE Books...</a></li></ul>
        </li>
        <li><a href="about.html">About US</a></li>
        <li><a href="contact.html">Contact Us</a></li>
        <li><a href="student-dashboard.php">Dashboard</a></li>
    </ul>
    <div class="user-profile dropdown-parent">
        <div class="profile-trigger">
            <div class="avatar-circle"><?php echo strtoupper(substr($user_name, 0, 1)); ?></div>
            <span><?php echo htmlspecialchars($user_name); ?> <i class="fas fa-chevron-down"></i></span>
        </div>
        <ul class="dropdown-menu profile-dropdown">
            <li class="profile-name-header"><?php echo htmlspecialchars($user_name); ?></li>
            <li><a href="student-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="student_profile.php"><i class="fas fa-user"></i> My Profile</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Account Settings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
</nav>

<header class="dash-header">
    <div class="big-avatar"><?php echo strtoupper(substr($user_name, 0, 1)); ?></div>
    <div class="welcome-text"><span>Hello,</span><h2><?php echo htmlspecialchars($user_name); ?></h2></div>
</header>

<div class="dashboard-container">
    <aside class="sidebar">
        <ul>
            <li><a href="student-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="student_profile.php"><i class="fas fa-user"></i> My Profile</a></li>
            <li><a href="my_courses.php"><i class="fas fa-graduation-cap"></i> Enrolled Courses</a></li>
            <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
            <li><a href="quiz_attempts.php"><i class="fas fa-chart-bar"></i> My Quiz Attempts</a></li>
            <li><a href="wishlist.php"><i class="fas fa-bookmark"></i> Wishlist</a></li>
            <li><a href="question-answer.php" class="active"><i class="fas fa-question-circle"></i> Question & Answer</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="dash-content">
        <h3>Question & Answer</h3>

        <?php if($message): ?>
            <div style="padding:15px; background:#d4edda; color:#155724; border-radius:5px; margin-bottom:20px;"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="ask-form-container">
            <h4 style="margin-top:0;">Ask a Doubt to Instructor</h4>
            <form action="question-answer.php" method="POST">
                <select name="course_id" class="form-control" required>
                    <option value="">Select Enrolled Course...</option>
                    <?php while($c = $courses_res->fetch_assoc()): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['title']); ?></option>
                    <?php endwhile; ?>
                </select>
                <textarea name="question_text" class="form-control" placeholder="Type your doubt here..." rows="3" required></textarea>
                <button type="submit" name="submit_question" class="btn-ask">Send Question</button>
            </form>
        </div>
        
        <div class="qa-header-controls">
            <span>Sort By:</span>
            <select class="qa-sort-select" onchange="location = 'question-answer.php?sort=' + this.value;">
                <option value="all" <?php if($filter == 'all') echo 'selected'; ?>>All(<?php echo $total_count; ?>)</option>
                <option value="answered" <?php if($filter == 'answered') echo 'selected'; ?>>Answered</option>
                <option value="unanswered" <?php if($filter == 'unanswered') echo 'selected'; ?>>Unanswered</option>
            </select>
        </div>

        <?php if ($qa_res->num_rows > 0): ?>
            <?php while($qa = $qa_res->fetch_assoc()): ?>
                <div class="qa-card">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                        <div>
                            <span style="font-weight:600; color:#3b82f6;"><?php echo htmlspecialchars($qa['course_title']); ?></span>
                            <span class="qa-badge <?php echo ($qa['status'] == 'answered') ? 'badge-answered' : 'badge-pending'; ?>">
                                <?php echo ucfirst($qa['status']); ?>
                            </span>
                        </div>
                        
                        <a href="question-answer.php?delete_qa=<?php echo $qa['id']; ?>" 
                           class="btn-delete-qa" 
                           onclick="return confirm('Delete this question permanentely?')" 
                           title="Delete Question">
                           <i class="fas fa-trash-alt"></i>
                        </a>
                    </div>

                    <div class="question-box">
                        <strong>Q:</strong> <?php echo htmlspecialchars($qa['question_text']); ?>
                        <div style="font-size:11px; color:#999; margin-top:5px;"><?php echo date('M d, Y h:i A', strtotime($qa['created_at'])); ?></div>
                    </div>
                    
                    <?php if($qa['admin_reply']): ?>
                        <div class="reply-box">
                            <strong style="color:#3b82f6;"><i class="fas fa-user-tie"></i> Instructor Reply:</strong><br>
                            <p style="margin: 8px 0;"><?php echo htmlspecialchars($qa['admin_reply']); ?></p>
                            <div style="font-size:11px; color:#999; margin-top:5px;">Replied on <?php echo date('M d, Y', strtotime($qa['replied_at'])); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="qa-empty-box">
                <i class="fas fa-search empty-icon" style="opacity: 0.3;"></i>
                <div class="empty-text">No Data Found.</div>
            </div>
        <?php endif; ?>

    </main>
</div>

<footer class="footer">
    <div class="footer-col">
        <img src="imgs/1.png" alt="Smart Learn" class="footer-logo">
        <p class="slogan">Learning often happens in<br> classrooms <br> but it doesn't have to.</p>
    </div>
    <div class="footer-col">
        <h4>Popular Content</h4>
        <ul>
            <li><a href="index.html">Home</a></li>
            <li><a href="courses.php">Course</a></li>
            <li><a href="student-dashboard.php">Dashboard</a></li>
            <li><a href="contact.html">Contact Us</a></li>
            <li><a href="about.html">About Us</a></li>
        </ul>
    </div>
    <div class="footer-col">
        <h4>Contact Info</h4>
        <p>0123456789</p>
        <p>smartlearnhelp@gmail.com</p>
    </div>
</footer>

</body>
</html>
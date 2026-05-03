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

// 2. FETCH AVAILABLE QUIZZES (Based on enrolled courses)
// FIXED: Added 'JOIN courses c ON q.course_id = c.id'
$sql_available = "SELECT q.id AS quiz_id, q.title AS quiz_title, c.title AS course_title 
                  FROM quizzes q 
                  JOIN enrollments e ON q.course_id = e.course_id 
                  JOIN courses c ON q.course_id = c.id 
                  WHERE e.user_id = $user_id 
                  ORDER BY q.created_at DESC";
$res_available = $conn->query($sql_available);

// 3. FETCH ATTEMPT HISTORY
$sql_history = "SELECT qa.*, q.title AS quiz_title, c.title AS course_title 
                FROM quiz_attempts qa 
                JOIN quizzes q ON qa.quiz_id = q.id 
                JOIN courses c ON q.course_id = c.id 
                WHERE qa.user_id = $user_id 
                ORDER BY qa.attempted_at DESC";
$res_history = $conn->query($sql_history);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Quiz Attempts - Smart Learn</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="imgs/favicon.png" type="image/png">

    <style>
        /* TABS STYLING */
        .tabs { display: flex; gap: 30px; border-bottom: 2px solid #f0f0f0; margin-bottom: 30px; }
        .tab-btn { background: none; border: none; padding: 10px 5px; cursor: pointer; color: #777; font-weight: 500; font-size: 16px; position: relative; font-family: 'Poppins', sans-serif; }
        .tab-btn.active { color: #3b82f6; font-weight: 600; }
        .tab-btn.active::after { content: ''; position: absolute; bottom: -2px; left: 0; width: 100%; height: 3px; background: #3b82f6; }

        .tab-content { display: none; animation: fadeIn 0.4s; }
        .tab-content.active-content { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* QUIZ CARDS (For Available Quizzes) */
        .quiz-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .quiz-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border: 1px solid #eee; display: flex; flex-direction: column; justify-content: space-between;}
        .quiz-card h4 { margin: 0 0 5px 0; color: #333; font-size: 18px; }
        .quiz-card p { margin: 0 0 20px 0; color: #777; font-size: 13px; }
        .btn-take-quiz { background: #3b82f6; color: white; text-align: center; padding: 10px; border-radius: 6px; text-decoration: none; font-weight: 500; transition: 0.2s; }
        .btn-take-quiz:hover { background: #2563eb; }

        /* TABLE (For History) */
        .table-container { background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px 20px; text-align: left; border-bottom: 1px solid #f0f0f0; }
        th { background: #f8f9fa; color: #555; font-weight: 600; font-size: 14px; }
        td { font-size: 14px; color: #333; }
        tr:last-child td { border-bottom: none; }
        
        .score-badge { padding: 5px 12px; border-radius: 20px; font-weight: bold; font-size: 13px; }
        .score-good { background: #d4edda; color: #155724; }
        .score-avg { background: #fff3cd; color: #856404; }
        .score-poor { background: #f8d7da; color: #721c24; }

        /* EMPTY STATE */
        .quiz-empty-box { background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); height: 300px; display: flex; flex-direction: column; align-items: center; justify-content: center; border: 1px solid #eee; }
        .empty-icon { font-size: 60px; color: #e0e0e0; margin-bottom: 15px; }
        .empty-text { font-weight: 500; color: #777; font-size: 16px; }
    </style>
</head>
<body>

<nav class="navbar dashboard-nav">
    <div class="logo">
        <a href="index.html"><img src="imgs/logo.jpg" alt="Smart Learn Logo"></a>
    </div>
    
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="courses.php">Course</a></li>
        <li class="dropdown-parent"><a href="#">Notes <i class="fas fa-chevron-down"></i></a>
            <ul class="dropdown-menu"><li><a href="#">IT / CE Notes...</a></li></ul> 
        </li>
        <li class="dropdown-parent"><a href="#">E-Book <i class="fas fa-chevron-down"></i></a>
            <ul class="dropdown-menu"><li><a href="#">IT / CE Books...</a></li></ul>
        </li>
        <li><a href="about.html">About US</a></li>
        <li><a href="contact.php">Contact Us</a></li>
        <li><a href="student-dashboard.php">Dashboard</a></li>
    </ul>
    
    <div class="user-profile dropdown-parent">
        <div class="profile-trigger">
            <div class="avatar-circle">
                <?php echo strtoupper(substr($user_name, 0, 1)); ?>
            </div>
            <span>
                <?php echo htmlspecialchars($user_name); ?> 
                <i class="fas fa-chevron-down"></i>
            </span>
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
    <div class="big-avatar">
        <?php echo strtoupper(substr($user_name, 0, 1)); ?>
    </div>
    <div class="welcome-text">
        <span>Hello,</span>
        <h2><?php echo htmlspecialchars($user_name); ?></h2>
    </div>
</header>

<div class="dashboard-container">
    
    <aside class="sidebar">
        <ul>
            <li><a href="student-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="student_profile.php"><i class="fas fa-user"></i> My Profile</a></li>
            <li><a href="my_courses.php"><i class="fas fa-graduation-cap"></i> Enrolled Courses</a></li>
            <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
            <li><a href="quiz_attempts.php" class="active"><i class="fas fa-chart-bar"></i> My Quiz Attempts</a></li>
            <li><a href="wishlist.php"><i class="fas fa-bookmark"></i> Wishlist</a></li>
           
            <li><a href="question-answer.php"><i class="fas fa-question-circle"></i> Question & Answer</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="dash-content">
        <h3>Quiz Center</h3>
        
        <div class="tabs">
            <button class="tab-btn active" onclick="openTab(event, 'available')">Available Quizzes</button>
            <button class="tab-btn" onclick="openTab(event, 'history')">My Attempt History</button>
        </div>

        <div id="available" class="tab-content active-content">
            <?php if ($res_available->num_rows > 0): ?>
                <div class="quiz-grid">
                    <?php while($quiz = $res_available->fetch_assoc()): ?>
                        <div class="quiz-card">
                            <div>
                                <h4><?php echo htmlspecialchars($quiz['quiz_title']); ?></h4>
                                <p><i class="fas fa-book" style="color:#3b82f6; margin-right:5px;"></i> <?php echo htmlspecialchars($quiz['course_title']); ?></p>
                            </div>
                            <a href="student_take_quiz.php?quiz_id=<?php echo $quiz['quiz_id']; ?>" class="btn-take-quiz">
                                <i class="fas fa-play-circle"></i> Start Quiz
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="quiz-empty-box">
                    <i class="fas fa-clipboard-check empty-icon"></i>
                    <div class="empty-text">No quizzes available for your enrolled courses yet.</div>
                </div>
            <?php endif; ?>
        </div>

        <div id="history" class="tab-content">
            <?php if ($res_history->num_rows > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Quiz Title</th>
                                <th>Date Attempted</th>
                                <th>Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $res_history->fetch_assoc()): 
                                // Calculate percentage for color coding
                                $percentage = ($row['score'] / $row['total_questions']) * 100;
                                if($percentage >= 70) $score_class = 'score-good';
                                elseif($percentage >= 40) $score_class = 'score-avg';
                                else $score_class = 'score-poor';
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['course_title']); ?></td>
                                <td><strong><?php echo htmlspecialchars($row['quiz_title']); ?></strong></td>
                                <td><?php echo date("d M Y, h:i A", strtotime($row['attempted_at'])); ?></td>
                                <td>
                                    <span class="score-badge <?php echo $score_class; ?>">
                                        <?php echo $row['score']; ?> / <?php echo $row['total_questions']; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="quiz-empty-box">
                    <i class="fas fa-history empty-icon"></i>
                    <div class="empty-text">You haven't attempted any quizzes yet.</div>
                </div>
            <?php endif; ?>
        </div>

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

<script>
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) { 
            tabcontent[i].style.display = "none"; 
            tabcontent[i].classList.remove("active-content"); 
        }
        
        tablinks = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tablinks.length; i++) { 
            tablinks[i].className = tablinks[i].className.replace(" active", ""); 
        }
        
        document.getElementById(tabName).style.display = "block";
        document.getElementById(tabName).classList.add("active-content");
        evt.currentTarget.className += " active";
    }
</script>

</body>
</html>
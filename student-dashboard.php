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

// 2. FETCH REAL STATS (Using JOIN to ignore deleted courses)

// A. Total Enrolled
$sql_total = "SELECT COUNT(*) as total 
              FROM enrollments e 
              JOIN courses c ON e.course_id = c.id 
              WHERE e.user_id = $user_id";
$res_total = $conn->query($sql_total);
$enrolled_count = $res_total->fetch_assoc()['total'];

// B. Active Courses
$sql_active = "SELECT COUNT(*) as total 
               FROM enrollments e 
               JOIN courses c ON e.course_id = c.id 
               WHERE e.user_id = $user_id AND e.status = 'active'";
$res_active = $conn->query($sql_active);
$active_count = $res_active->fetch_assoc()['total'];

// C. Completed Courses
$sql_completed = "SELECT COUNT(*) as total 
                  FROM enrollments e 
                  JOIN courses c ON e.course_id = c.id 
                  WHERE e.user_id = $user_id AND e.status = 'completed'";
$res_completed = $conn->query($sql_completed);
$completed_count = $res_completed->fetch_assoc()['total'];

// D. FETCH ENROLLED COURSES FOR PROGRESS LIST
$sql_prog = "SELECT c.id, c.title FROM enrollments e 
             JOIN courses c ON e.course_id = c.id 
             WHERE e.user_id = $user_id";
$res_prog = $conn->query($sql_prog);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Smart Learn</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="imgs/favicon.png" type="image/png">
    
    <style>
        /* PROGRESS SECTION CSS - Shielded to prevent alignment issues */
        .progress-section-title {
            margin: 40px 0 20px;
            font-size: 20px;
            color: #2c3e50;
            border-bottom: 2px solid #f0f2f5;
            padding-bottom: 10px;
        }
        
        .progress-card-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
            width: 100%;
        }

        .prog-card {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: 1px solid #eef0f2;
            transition: transform 0.2s;
        }

        .prog-card:hover {
            transform: translateY(-2px);
        }

        .prog-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .prog-info h4 {
            margin: 0;
            font-size: 15px;
            color: #333;
            font-weight: 600;
        }

        .prog-percent {
            font-weight: 700;
            color: #2ecc71;
            font-size: 14px;
        }

        .bar-bg {
            background: #f0f2f5;
            height: 10px;
            border-radius: 20px;
            width: 100%;
            overflow: hidden;
            margin-bottom: 15px;
        }

        .bar-fill {
            background: #2ecc71;
            height: 100%;
            border-radius: 20px;
            transition: width 0.8s ease-in-out;
        }

        .btn-continue {
            text-decoration: none;
            color: #3498db;
            font-size: 13px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-continue:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<nav class="navbar dashboard-nav">
    <div class="logo">
        <a href="#"><img src="imgs/logo.jpg" alt="Smart Learn Logo"></a>
    </div>
    
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="courses.php">Course</a></li>
        
        <li class="dropdown-parent">
            <a href="#">Notes <i class="fas fa-chevron-down"></i></a>
            <ul class="dropdown-menu">
                <li class="dropdown-sub-parent">
                    <a href="#">Information Technology (IT) <i class="fas fa-chevron-right arrow-right"></i></a>
                    <ul class="submenu">
                        <li><a href="view_notes.php?branch=IT&year=1st Year">1st Year</a></li>
                        <li><a href="view_notes.php?branch=IT&year=2nd Year">2nd Year</a></li>
                        <li><a href="view_notes.php?branch=IT&year=3rd Year">3rd Year</a></li>
                        <li><a href="view_notes.php?branch=IT&year=4th Year">4th Year</a></li>
                    </ul>
                </li>
                <li class="dropdown-sub-parent">
                    <a href="#">Computer Engineering (CE) <i class="fas fa-chevron-right arrow-right"></i></a>
                    <ul class="submenu">
                        <li><a href="view_notes.php?branch=CE&year=1st Year">1st Year</a></li>
                        <li><a href="view_notes.php?branch=CE&year=2nd Year">2nd Year</a></li>
                        <li><a href="view_notes.php?branch=CE&year=3rd Year">3rd Year</a></li>
                        <li><a href="view_notes.php?branch=CE&year=4th Year">4th Year</a></li>
                    </ul>
                </li>
            </ul>
        </li>

        <li class="dropdown-parent">
            <a href="#">E-Book <i class="fas fa-chevron-down"></i></a>
            <ul class="dropdown-menu">
                <li class="dropdown-sub-parent">
                    <a href="#">Information Technology (IT) <i class="fas fa-chevron-right arrow-right"></i></a>
                    <ul class="submenu">
                        <li><a href="view_ebooks.php?branch=IT&year=1st Year">1st Year</a></li>
                        <li><a href="view_ebooks.php?branch=IT&year=2nd Year">2nd Year</a></li>
                        <li><a href="view_ebooks.php?branch=IT&year=3rd Year">3rd Year</a></li>
                        <li><a href="view_ebooks.php?branch=IT&year=4th Year">4th Year</a></li>
                    </ul>
                </li>
                <li class="dropdown-sub-parent">
                    <a href="#">Computer Engineering (CE) <i class="fas fa-chevron-right arrow-right"></i></a>
                    <ul class="submenu">
                        <li><a href="view_ebooks.php?branch=CE&year=1st Year">1st Year</a></li>
                        <li><a href="view_ebooks.php?branch=CE&year=2nd Year">2nd Year</a></li>
                        <li><a href="view_ebooks.php?branch=CE&year=3rd Year">3rd Year</a></li>
                        <li><a href="view_ebooks.php?branch=CE&year=4th Year">4th Year</a></li>
                    </ul>
                </li>
            </ul>
        </li>
        
        <li><a href="about.html">About US</a></li>
        <li><a href="contact.php">Contact Us</a></li>
        <li><a href="#" class="active">Dashboard</a></li>
    </ul>
    
    <div class="user-profile dropdown-parent">
        <div class="profile-trigger">
            <div class="avatar-circle">
                <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
            </div>
            <span>
                <?php echo htmlspecialchars($_SESSION['user_name']); ?> 
                <i class="fas fa-chevron-down"></i>
            </span>
        </div>

        <ul class="dropdown-menu profile-dropdown">
            <li class="profile-name-header"><?php echo htmlspecialchars($_SESSION['user_name']); ?></li>
            <li><a href="student-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="student_profile.php"><i class="fas fa-user"></i> My Profile</a></li>
            <li><a href="settings.html"><i class="fas fa-cog"></i> Account Settings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
</nav>

<header class="dash-header">
    <div class="big-avatar">
        <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
    </div>
    
    <div class="welcome-text">
        <span>Hello,</span>
        <h2><?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>
    </div>
</header>

<div class="dashboard-container">
    
    <aside class="sidebar">
        <ul>
            <li><a href="student-dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="student_profile.php"><i class="fas fa-user"></i> My Profile</a></li>
            <li><a href="my_courses.php"><i class="fas fa-graduation-cap"></i> Enrolled Courses</a></li>
            <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
            <li><a href="quiz_attempts.php"><i class="fas fa-chart-bar"></i> My Quiz Attempts</a></li>
            <li><a href="wishlist.php"><i class="fas fa-bookmark"></i> Wishlist</a></li>
            <li><a href="question-answer.php"><i class="fas fa-question-circle"></i> Question & Answer</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="dash-content">
        <h3>Dashboard</h3>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon-box blue-icon"><i class="fas fa-book-open"></i></div>
                <h2><?php echo $enrolled_count; ?></h2>
                <p>Enrolled Courses</p>
            </div>

            <div class="stat-card">
                <div class="icon-box blue-icon"><i class="fas fa-graduation-cap"></i></div>
                <h2><?php echo $active_count; ?></h2>
                <p>Active Courses</p>
            </div>

            <div class="stat-card">
                <div class="icon-box blue-icon"><i class="fas fa-trophy"></i></div>
                <h2><?php echo $completed_count; ?></h2>
                <p>Completed Courses</p>
            </div>
        </div>

        <h3 class="progress-section-title">My Learning Progress</h3>
        <div class="progress-card-container">
            <?php if ($res_prog->num_rows > 0): ?>
                <?php while($course_row = $res_prog->fetch_assoc()): 
                    $p_val = getCourseProgress($conn, $user_id, $course_row['id']); 
                ?>
                <div class="prog-card">
                    <div class="prog-info">
                        <h4><?php echo htmlspecialchars($course_row['title']); ?></h4>
                        <span class="prog-percent"><?php echo $p_val; ?>%</span>
                    </div>
                    <div class="bar-bg">
                        <div class="bar-fill" style="width: <?php echo $p_val; ?>%;"></div>
                    </div>
                    <a href="course_player.php?course_id=<?php echo $course_row['id']; ?>" class="btn-continue">
                        Continue Learning <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="background: white; padding: 20px; border-radius: 12px; color: #888; text-align:center;">
                    No courses enrolled yet. <a href="courses.php" style="color:#3498db;">Explore Courses</a>
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
            <li><a href="index.php">Home</a></li>
            <li><a href="courses.php">Course</a></li>
            <li><a href="student-dashboard.php">Dashboard</a></li>
            <li><a href="contact.php">Contact Us</a></li>
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
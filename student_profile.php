<?php
session_start();
require 'db_connect.php';

// 1. SECURITY CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. FETCH USER DETAILS
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// 3. PREPARE DATA (Handle missing values)
$reg_date = date("F j, Y g:i a", strtotime($user['created_at'])); // e.g., January 9, 2026 5:23 pm
$fname = htmlspecialchars($user['First_Name']);
$lname = htmlspecialchars($user['Last_Name']);
$username = htmlspecialchars($user['User_Name']);
$email = htmlspecialchars($user['email']);

// Check if these columns exist (in case you haven't run the SQL yet)
$phone = isset($user['phone']) ? htmlspecialchars($user['phone']) : '-';
$skill = isset($user['skill']) ? htmlspecialchars($user['skill']) : '-';
$bio = isset($user['bio']) ? htmlspecialchars($user['bio']) : '-';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - Smart Learn</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="imgs/favicon.png" type="image/png">
    
    <style>
        /* PROFILE SPECIFIC CSS */
        .profile-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .profile-header {
            background: #3b82f6; /* Blue Color from screenshot */
            color: white;
            padding: 15px 20px;
            border-radius: 6px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 25px;
            min-width: 150px;
            text-align: center;
        }

        .profile-row {
            display: flex;
            border-bottom: 1px solid #f0f0f0;
            padding: 15px 0;
            align-items: center;
        }

        .profile-row:last-child {
            border-bottom: none;
        }

        .profile-label {
            width: 200px;
            color: #777;
            font-weight: 500;
            font-size: 14px;
        }

        .profile-value {
            font-weight: 500;
            color: #333;
            font-size: 15px;
        }
        
        @media (max-width: 768px) {
            .profile-row { flex-direction: column; align-items: flex-start; gap: 5px; }
            .profile-label { width: 100%; font-size: 13px; }
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
        <li><a href="student-dashboard.php">Dashboard</a></li>
    </ul>
    
    <div class="user-profile dropdown-parent">
        <div class="profile-trigger">
            <div class="avatar-circle">
                <?php echo strtoupper(substr($user['User_Name'], 0, 1)); ?>
            </div>
            <span>
                <?php echo htmlspecialchars($user['User_Name']); ?> 
                <i class="fas fa-chevron-down"></i>
            </span>
        </div>

        <ul class="dropdown-menu profile-dropdown">
            <li class="profile-name-header">
                <?php echo htmlspecialchars($user['User_Name']); ?>
            </li>
            <li><a href="student-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="student_profile.php"><i class="fas fa-user"></i> My Profile</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Account Settings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
</nav>

<header class="dash-header">
    <div class="big-avatar">
        <?php echo strtoupper(substr($user['User_Name'], 0, 1)); ?>
    </div>
    
    <div class="welcome-text">
        <span>Hello,</span>
        <h2><?php echo htmlspecialchars($user['User_Name']); ?></h2>
    </div>
</header>

<div class="dashboard-container">
    
    <aside class="sidebar">
        <ul>
            <li><a href="student-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="student_profile.php" class="active"><i class="fas fa-user"></i> My Profile</a></li>
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
        <h3>My Profile</h3>
        
        <div class="profile-container">
            <div class="profile-header"><i class="fas fa-user"></i> My Profile</div>

            <div class="profile-row">
                <div class="profile-label">Registration Date</div>
                <div class="profile-value"><?php echo $reg_date; ?></div>
            </div>

            <div class="profile-row">
                <div class="profile-label">First Name</div>
                <div class="profile-value"><?php echo $fname; ?></div>
            </div>

            <div class="profile-row">
                <div class="profile-label">Last Name</div>
                <div class="profile-value"><?php echo $lname; ?></div>
            </div>

            <div class="profile-row">
                <div class="profile-label">Username</div>
                <div class="profile-value"><?php echo $username; ?></div>
            </div>

            <div class="profile-row">
                <div class="profile-label">Email</div>
                <div class="profile-value"><?php echo $email; ?></div>
            </div>

            <div class="profile-row">
                <div class="profile-label">Phone Number</div>
                <div class="profile-value"><?php echo $phone; ?></div>
            </div>

            <div class="profile-row">
                <div class="profile-label">Skill/Occupation</div>
                <div class="profile-value"><?php echo $skill; ?></div>
            </div>

            <div class="profile-row">
                <div class="profile-label">Biography</div>
                <div class="profile-value"><?php echo $bio; ?></div>
            </div>

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
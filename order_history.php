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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order History - Smart Learn</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="imgs/favicon.png" type="image/png">

    <style>
        /* ORDER HISTORY SPECIFIC CSS */
        .order-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-filter {
            padding: 8px 25px;
            border: 1px solid #3b82f6; /* Blue border */
            border-radius: 5px;
            background: white;
            color: #3b82f6;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-filter:hover {
            background: #e3f2fd;
        }

        /* Active Button Style (Solid Blue) */
        .btn-filter.active {
            background: #3b82f6;
            color: white;
        }

        .date-picker {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            color: #777;
            font-family: 'Poppins', sans-serif;
            outline: none;
        }

       
        .empty-state {
            text-align: center;
            padding: 50px;
            color: #777;
        }
        .empty-state img { width: 200px; opacity: 0.8; margin-bottom: 20px; }
        .empty-text { font-size: 14px; color: #777; }
    </style>
</head>
<body>

<nav class="navbar dashboard-nav">
    <div class="logo">
        <a href="index.html"><img src="imgs/logo.jpg" alt="Smart Learn Logo"></a>
    </div>
    
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
            <li><a href="settings.html"><i class="fas fa-cog"></i> Account Settings</a></li>
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
            <li><a href="quiz_attempts.php"><i class="fas fa-chart-bar"></i> My Quiz Attempts</a></li>
            <li><a href="wishlist.php"><i class="fas fa-bookmark"></i> Wishlist</a></li>
            
            <li><a href="order_history.php" class="active"><i class="fas fa-shopping-cart"></i> Order History</a></li>
            
            <li><a href="question-answer.php"><i class="fas fa-question-circle"></i> Question & Answer</a></li>
            <li><a href="settings.html"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="dash-content">
        <h3>Order History</h3>
        
        <div class="order-toolbar">
            <div class="filter-buttons">
                <button class="btn-filter active">Today</button>
                <button class="btn-filter">Monthly</button>
                <button class="btn-filter">Yearly</button>
            </div>
            
            <input type="date" class="date-picker">
        </div>

        <div class="empty-state">
            <img src="imgs/empty-mailbox.png" alt="No Data Found">
            <div class="empty-text">No Data Available in this Section</div>
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
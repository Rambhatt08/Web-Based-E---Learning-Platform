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

// 2. FETCH WISHLIST COURSES
$sql = "SELECT c.* FROM courses c 
        JOIN wishlist w ON c.id = w.course_id 
        WHERE w.user_id = $user_id 
        ORDER BY w.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Wishlist - Smart Learn</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="imgs/favicon.png" type="image/png">

    <style>
        /* GRID FOR WISHLIST ITEMS */
        .wishlist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
            gap: 25px;
        }

        /* CARD STYLING (Reusing your Course Card style) */
        .wish-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: 0.3s;
            border: 1px solid #eee;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .wish-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        
        .wish-card-img { width: 100%; height: 160px; object-fit: cover; }
        .wish-card-body { padding: 15px; flex-grow: 1; display: flex; flex-direction: column; }
        .wish-card-title { font-size: 16px; font-weight: 600; margin-bottom: 8px; color: #333; }
        .wish-card-desc { font-size: 13px; color: #666; margin-bottom: 15px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        
        /* Remove Button */
        .btn-remove {
            background: #ffebee;
            color: #c62828;
            border: none;
            padding: 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            margin-top: auto;
            text-align: center;
            width: 100%;
        }
        .btn-remove:hover { background: #ffcdd2; }

        /* EMPTY STATE (Matches your screenshot) */
        .empty-state {
            text-align: center;
            padding: 50px;
            color: #777;
        }
        .empty-state img { width: 150px; opacity: 0.6; margin-bottom: 20px; }
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
            
            <li><a href="wishlist.php" class="active"><i class="fas fa-bookmark"></i> Wishlist</a></li>
            
            
            <li><a href="question-answer.php"><i class="fas fa-question-circle"></i> Question & Answer</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="dash-content">
        <h3>My Wishlist</h3>
        
        <?php if ($result->num_rows > 0): ?>
            <div class="wishlist-grid">
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="wish-card">
                        
                        <?php 
                            $img_path = !empty($row['thumbnail']) ? $row['thumbnail'] : 'imgs/default-course.jpg';
                            if (!file_exists($img_path) && strpos($img_path, 'http') === false) $img_path = 'https://via.placeholder.com/400x250?text=Course'; 
                        ?>
                        <img src="<?php echo $img_path; ?>" class="wish-card-img" alt="Course">
                        
                        <div class="wish-card-body">
                            <div class="wish-card-title"><?php echo htmlspecialchars($row['title']); ?></div>
                            <div class="wish-card-desc"><?php echo htmlspecialchars($row['description']); ?></div>
                            
                            <div style="display:flex; gap:10px; margin-top:auto;">
                                <a href="courses.php" style="flex:1; background:#3b82f6; color:white; text-align:center; padding:8px; border-radius:4px; font-size:12px;">View</a>
                                
                                <form action="wishlist_process.php" method="POST" style="flex:1;">
                                    <input type="hidden" name="course_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="redirect" value="wishlist.php">
                                    <button type="submit" class="btn-remove"><i class="fas fa-trash"></i> Remove</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

        <?php else: ?>
            
            <div class="empty-state">
                <img src="imgs/empty-mailbox.png" alt="No Wishlist Items" style="width:500px; opacity:1.5;">
                <p>No Data Available in this Section</p>
                <a href="courses.php" style="color:#3b82f6; font-weight:600; margin-top:10px; display:inline-block;">Browse Courses to Add</a>
                
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
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

// 2. HANDLE FORM SUBMISSION (Write a Review)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    $course_id = intval($_POST['course_id']);
    $rating = intval($_POST['rating']);
    $comment = $conn->real_escape_string($_POST['comment']); // Using 'comment' based on your table

    // Check duplicate
    $check = $conn->query("SELECT id FROM reviews WHERE user_id = $user_id AND course_id = $course_id");
    
    if ($check->num_rows == 0) {
        $sql_insert = "INSERT INTO reviews (user_id, course_id, rating, comment) VALUES ($user_id, $course_id, $rating, '$comment')";
        if ($conn->query($sql_insert)) {
            $message = "Review submitted successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
    } else {
        $message = "You have already reviewed this course.";
    }
}

// 3. FETCH DATA

// A. PENDING REVIEWS (Courses owned but NOT in reviews table)
$sql_pending = "SELECT c.*, c.thumbnail 
                FROM courses c 
                JOIN enrollments e ON c.id = e.course_id 
                WHERE e.user_id = $user_id 
                AND c.id NOT IN (SELECT course_id FROM reviews WHERE user_id = $user_id)";
$res_pending = $conn->query($sql_pending);

// B. REVIEW HISTORY (Your existing query)
$sql_history = "SELECT r.*, c.title AS course_title, c.thumbnail 
                FROM reviews r 
                JOIN courses c ON r.course_id = c.id 
                WHERE r.user_id = $user_id 
                ORDER BY r.created_at DESC";
$res_history = $conn->query($sql_history);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Reviews - Smart Learn</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="imgs/favicon.png" type="image/png">

    <style>
        /* TABS Styling */
        .tabs { display: flex; gap: 30px; border-bottom: 2px solid #f0f0f0; margin-bottom: 30px; }
        .tab-btn { background: none; border: none; padding: 10px 5px; cursor: pointer; color: #777; font-weight: 500; font-size: 16px; position: relative; font-family: 'Poppins', sans-serif; }
        .tab-btn.active { color: #3b82f6; font-weight: 600; }
        .tab-btn.active::after { content: ''; position: absolute; bottom: -2px; left: 0; width: 100%; height: 3px; background: #3b82f6; }
        
        .tab-content { display: none; animation: fadeIn 0.5s; }
        .tab-content.active-content { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* REVIEW CARD STYLING (Reused yours) */
        .review-card {
            background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; display: flex; gap: 20px; border: 1px solid #f0f0f0;
        }
        .review-img { width: 80px; height: 80px; object-fit: cover; border-radius: 6px; } /* Slightly larger for Pending form */
        .review-content { flex: 1; }
        .review-header { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .course-name { font-weight: 600; color: #333; font-size: 16px; }
        .review-date { font-size: 12px; color: #999; }
        .star-rating { color: #f1c40f; font-size: 14px; margin-bottom: 8px; }
        .review-text { color: #555; font-size: 14px; line-height: 1.5; }

        /* FORM STYLES */
        .form-group { margin-bottom: 15px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Poppins', sans-serif; }
        textarea.form-control { resize: vertical; min-height: 80px; }
        .btn-submit { background: #3b82f6; color: white; border: none; padding: 8px 20px; border-radius: 5px; cursor: pointer; font-size: 14px; transition: 0.3s; }
        .btn-submit:hover { background: #2563eb; }

        .alert { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .empty-state { text-align: center; padding: 50px; color: #777; }
        .empty-state img { width: 150px; opacity: 0.6; margin-bottom: 20px; }
    </style>
</head>
<body>

<nav class="navbar dashboard-nav">
    <div class="logo"><a href="index.html"><img src="imgs/logo.jpg" alt="Logo"></a></div>
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
            <li><a href="reviews.php" class="active"><i class="fas fa-star"></i> Reviews</a></li>
            <li><a href="quiz_attempts.php"><i class="fas fa-chart-bar"></i> My Quiz Attempts</a></li>
            <li><a href="wishlist.php"><i class="fas fa-bookmark"></i> Wishlist</a></li>
            
            <li><a href="question-answer.php"><i class="fas fa-question-circle"></i> Question & Answer</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="dash-content">
        <h3>Reviews</h3>
        
        <?php if($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="tabs">
            <button class="tab-btn active" onclick="openTab(event, 'pending')">Pending Reviews</button>
            <button class="tab-btn" onclick="openTab(event, 'history')">Review History</button>
        </div>

        <div id="pending" class="tab-content active-content">
            <?php if($res_pending->num_rows > 0): ?>
                <?php while($row = $res_pending->fetch_assoc()): ?>
                    <div class="review-card">
                        <?php 
                            $img_path = !empty($row['thumbnail']) ? $row['thumbnail'] : 'imgs/default-course.jpg';
                            if (!file_exists($img_path) && strpos($img_path, 'http') === false) $img_path = 'https://via.placeholder.com/80x50?text=Course'; 
                        ?>
                        <img src="<?php echo $img_path; ?>" class="review-img" alt="Course">
                        
                        <div class="review-content">
                            <div class="review-header">
                                <div class="course-name"><?php echo htmlspecialchars($row['title']); ?></div>
                            </div>
                            
                            <form action="reviews.php" method="POST">
                                <input type="hidden" name="course_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="submit_review" value="1">
                                
                                <div class="form-group">
                                    <select name="rating" class="form-control" required>
                                        <option value="" disabled selected>Select Rating</option>
                                        <option value="5">⭐⭐⭐⭐⭐ (Excellent)</option>
                                        <option value="4">⭐⭐⭐⭐ (Good)</option>
                                        <option value="3">⭐⭐⭐ (Average)</option>
                                        <option value="2">⭐⭐ (Poor)</option>
                                        <option value="1">⭐ (Terrible)</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <textarea name="comment" class="form-control" placeholder="Write your experience with this course..." required></textarea>
                                </div>
                                <button type="submit" class="btn-submit">Submit Review</button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <img src="imgs/empty-mailbox.png" alt="No Reviews" style="width:500px; opacity:1.5;">
                    <p>You have reviewed all your enrolled courses! Great job.</p>
                </div>
            <?php endif; ?>
        </div>

        <div id="history" class="tab-content">
            <?php if ($res_history->num_rows > 0): ?>
                <?php while($row = $res_history->fetch_assoc()): ?>
                    <div class="review-card">
                        <?php 
                            $img_path = !empty($row['thumbnail']) ? $row['thumbnail'] : 'imgs/default-course.jpg';
                            if (!file_exists($img_path) && strpos($img_path, 'http') === false) $img_path = 'https://via.placeholder.com/80x50?text=Course'; 
                        ?>
                        <img src="<?php echo $img_path; ?>" class="review-img" alt="Course">
                        
                        <div class="review-content">
                            <div class="review-header">
                                <div class="course-name"><?php echo htmlspecialchars($row['course_title']); ?></div>
                                <div class="review-date"><?php echo date("M d, Y", strtotime($row['created_at'])); ?></div>
                            </div>
                            
                            <div class="star-rating">
                                <?php 
                                    for($i=1; $i<=5; $i++) {
                                        if($i <= $row['rating']) echo '<i class="fas fa-star"></i>';
                                        else echo '<i class="far fa-star"></i>';
                                    }
                                ?>
                            </div>
                            
                            <p class="review-text">"<?php echo htmlspecialchars($row['comment']); ?>"</p>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <img src="imgs/empty-mailbox.png" alt="No Reviews" style="width:500px; opacity:1.5;">
                    <p>No reviews submitted yet.</p>
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
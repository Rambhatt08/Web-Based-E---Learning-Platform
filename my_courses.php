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

// 2. FETCH ENROLLED COURSES (Include the 'status' column)
$sql = "SELECT c.*, e.status as enrollment_status 
        FROM courses c 
        JOIN enrollments e ON c.id = e.course_id 
        WHERE e.user_id = $user_id 
        ORDER BY e.enrolled_at DESC";

$result = $conn->query($sql);

// Store courses in an array
$all_courses = [];
while ($row = $result->fetch_assoc()) {
    $all_courses[] = $row;
}

// Check for tab parameter to auto-open correct tab
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'enrolled';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enrolled Courses - Smart Learn</title>
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

        .tab-content { display: none; animation: fadeIn 0.5s; }
        .tab-content.active-content { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* COURSE CARD */
        .enrolled-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; }
        .dash-course-card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: 0.3s; border: 1px solid #eee; display: flex; flex-direction: column; }
        .dash-course-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .dash-card-img { width: 100%; height: 160px; object-fit: cover; }
        .dash-card-body { padding: 15px; flex-grow: 1; display: flex; flex-direction: column; }
        .dash-card-title { font-size: 16px; font-weight: 600; margin-bottom: 8px; color: #333; }
        .dash-card-desc { font-size: 13px; color: #666; margin-bottom: 15px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        
        .btn-start-learn { margin-top: auto; background: #27ae60; color: white; text-align: center; padding: 10px; border-radius: 6px; font-size: 14px; font-weight: 500; transition: 0.2s; display: block; }
        .btn-start-learn:hover { background: #219150; }

        /* COMPLETE BUTTON */
        .btn-mark-complete {
            background: none; border: 1px solid #3b82f6; color: #3b82f6;
            padding: 8px; width: 100%; border-radius: 6px; margin-top: 10px;
            cursor: pointer; font-size: 13px; font-weight: 500; transition: 0.2s;
        }
        .btn-mark-complete:hover { background: #e3f2fd; }

        .completed-badge {
            background: #e8f5e9; color: #2e7d32; text-align: center;
            padding: 8px; border-radius: 6px; margin-top: 10px; font-size: 13px; font-weight: 600;
        }

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
            <li><a href="my_courses.php" class="active"><i class="fas fa-graduation-cap"></i> Enrolled Courses</a></li>
            <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
            <li><a href="quiz_attempts.php"><i class="fas fa-chart-bar"></i> My Quiz Attempts</a></li>
            <li><a href="wishlist.php"><i class="fas fa-bookmark"></i> Wishlist</a></li>
            
            <li><a href="question-answer.php"><i class="fas fa-question-circle"></i> Question & Answer</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="dash-content">
        <h3>Enrolled Courses</h3>
        
        <div class="tabs">
            <button class="tab-btn <?php echo ($active_tab == 'enrolled') ? 'active' : ''; ?>" onclick="openTab(event, 'enrolled')">Enrolled Courses</button>
            <button class="tab-btn <?php echo ($active_tab == 'active') ? 'active' : ''; ?>" onclick="openTab(event, 'active')">Active Courses</button>
            <button class="tab-btn <?php echo ($active_tab == 'completed') ? 'active' : ''; ?>" onclick="openTab(event, 'completed')">Completed Courses</button>
        </div>

        <div id="enrolled" class="tab-content <?php echo ($active_tab == 'enrolled') ? 'active-content' : ''; ?>">
            <?php if (count($all_courses) > 0): ?>
                <div class="enrolled-grid">
                    <?php foreach ($all_courses as $course): ?>
                        <div class="dash-course-card">
                            <img src="<?php echo !empty($course['thumbnail']) ? $course['thumbnail'] : 'imgs/default-course.jpg'; ?>" class="dash-card-img" alt="Course">
                            <div class="dash-card-body">
                                <div class="dash-card-title"><?php echo htmlspecialchars($course['title']); ?></div>
                                <a href="course_player.php?course_id=<?php echo $course['id']; ?>" class="btn-start-learn"><i class="fas fa-play-circle"></i> Continue Learning</a>
                                
                                <?php if($course['enrollment_status'] == 'completed'): ?>
                                    <div class="completed-badge"><i class="fas fa-check-circle"></i> Completed</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state"><img src="imgs/empty-mailbox.png"alt="No Data" style="width:500px; opacity:1.5;"><p>No Enrolled Courses Found.</p></div>
            <?php endif; ?>
        </div>

        <div id="active" class="tab-content <?php echo ($active_tab == 'active') ? 'active-content' : ''; ?>">
            <div class="enrolled-grid">     
                <?php 
                $has_active = false;
                foreach ($all_courses as $course): 
                    if($course['enrollment_status'] == 'active'):
                        $has_active = true;
                ?>
                    <div class="dash-course-card">
                        <img src="<?php echo !empty($course['thumbnail']) ? $course['thumbnail'] : 'imgs/default-course.jpg'; ?>" class="dash-card-img" alt="Course">
                        <div class="dash-card-body">
                            <div class="dash-card-title"><?php echo htmlspecialchars($course['title']); ?></div>
                            <a href="course_player.php?course_id=<?php echo $course['id']; ?>" class="btn-start-learn"><i class="fas fa-play-circle"></i> Resume Learning</a>
                            
                            <form action="mark_complete.php" method="POST">
                                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                <button type="submit" class="btn-mark-complete" onclick="return confirm('Are you sure you finished this course?')">Mark as Completed</button>
                            </form>
                        </div>
                    </div>
                <?php 
                    endif; 
                endforeach; 
                
                if(!$has_active): ?>
                    <div class="empty-state" style="grid-column: 1/-1;"><img src="imgs/empty-mailbox.png" alt="No Data" style="width:500px; opacity:1.5;"><p>No Active Courses.</p></div>
                <?php endif; ?>
            </div>
        </div>

        <div id="completed" class="tab-content <?php echo ($active_tab == 'completed') ? 'active-content' : ''; ?>">
            <div class="enrolled-grid">
                <?php 
                $has_completed = false;
                foreach ($all_courses as $course): 
                    if($course['enrollment_status'] == 'completed'):
                        $has_completed = true;
                ?>
                    <div class="dash-course-card">
                        <img src="<?php echo !empty($course['thumbnail']) ? $course['thumbnail'] : 'imgs/default-course.jpg'; ?>" class="dash-card-img" alt="Course" style="filter: grayscale(100%);">
                        <div class="dash-card-body">
                            <div class="dash-card-title"><?php echo htmlspecialchars($course['title']); ?></div>
                            <a href="course_player.php?course_id=<?php echo $course['id']; ?>" class="btn-start-learn" style="background:#7f8c8d;">Review Course</a>
                            <div class="completed-badge"><i class="fas fa-check-circle"></i> Completed</div>
                        </div>
                    </div>
                <?php 
                    endif; 
                endforeach; 
                
                if(!$has_completed): ?>
                    <div class="empty-state" style="grid-column: 1/-1;">
                        <img src="imgs/empty-mailbox.png" alt="No Data" style="width:500px; opacity:1.5;" ><p>No Completed Courses Yet.</p></div>
                <?php endif; ?>
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

<script>
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) { tabcontent[i].style.display = "none"; tabcontent[i].classList.remove("active-content"); }
        tablinks = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tablinks.length; i++) { tablinks[i].className = tablinks[i].className.replace(" active", ""); }
        document.getElementById(tabName).style.display = "block";
        document.getElementById(tabName).classList.add("active-content");
        evt.currentTarget.className += " active";
    }
</script>



</body>
</html>
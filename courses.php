<?php
session_start();
require 'db_connect.php';

// Check if user is logged in
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest';
$enrolled_courses = [];
$wishlist_courses = []; // <--- 1. Initialize Wishlist Array

// If logged in, fetch the list of courses they already own AND their wishlist
if ($user_id > 0) {
    // Fetch Enrolled Courses
    $enroll_query = $conn->query("SELECT course_id FROM enrollments WHERE user_id = $user_id");
    while($row = $enroll_query->fetch_assoc()) {
        $enrolled_courses[] = $row['course_id'];
    }

    // Fetch Wishlist Courses <--- 2. New Logic
    $wish_query = $conn->query("SELECT course_id FROM wishlist WHERE user_id = $user_id");
    while($row = $wish_query->fetch_assoc()) {
        $wishlist_courses[] = $row['course_id'];
    }
}

// --- SEARCH LOGIC ---
$search_term = "";
if (isset($_GET['search'])) {
    $search_term = $conn->real_escape_string($_GET['search']);
    // Filter by Title or Level
    $sql = "SELECT * FROM courses WHERE title LIKE '%$search_term%' OR level LIKE '%$search_term%' ORDER BY id DESC";
} else {
    // Show All
    $sql = "SELECT * FROM courses ORDER BY id DESC";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Courses - Smart Learn</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="imgs/favicon.png" type="image/png">
    <style>
        /* --- GLOBAL RESET & FONTS --- */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background-color: #f8f9fa; color: #333; }
        a { text-decoration: none; color: inherit; }
        ul { list-style: none; }

        /* --- NAVBAR STYLING --- */
        .navbar {
            background: white;
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .logo img { height: 70px; width: auto; }
        .nav-links { display: flex; gap: 30px; align-items: center; }
        .nav-links li { position: relative; }
        .nav-links a { font-weight: 500; color: #555; font-size: 16px; transition: 0.3s; display: flex; align-items: center; gap: 5px; }
        .nav-links a:hover, .nav-links a.active { color: #8e44ad; }

        /* DROPDOWN MENUS */
        .dropdown-menu {
            position: absolute; top: 100%; left: 0; background: white; width: 220px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); border-radius: 8px; padding: 10px 0;
            opacity: 0; visibility: hidden; transform: translateY(10px); transition: 0.3s ease; z-index: 1001;
        }
        .dropdown-parent:hover .dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0); }
        .dropdown-menu li a { padding: 10px 20px; display: block; font-size: 14px; color: #333; }
        .dropdown-menu li a:hover { background: #f4f7f6; color: #8e44ad; }

        /* SUBMENU */
        .submenu { position: absolute; top: 0; left: 100%; background: white; width: 200px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); border-radius: 8px; opacity: 0; visibility: hidden; transition: 0.3s; }
        .dropdown-sub-parent:hover .submenu { opacity: 1; visibility: visible; }

        /* USER PROFILE */
        .user-profile { display: flex; align-items: center; gap: 10px; cursor: pointer; position: relative; }
        .avatar-circle { width: 40px; height: 40px; background: #3498db; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 18px; }
        .profile-name { font-weight: 600; color: #333; display: flex; align-items: center; gap: 5px; }
        .profile-dropdown { right: 0; left: auto; width: 180px; }
        .btn-login { background: #8e44ad; color: white !important; padding: 8px 20px; border-radius: 20px; }
        .btn-login:hover { background: #732d91; }

        /* --- PAGE HEADER --- */
        .page-header { background: linear-gradient(135deg, #8e44ad, #9b59b6); color: white; padding: 60px 20px 80px 20px; text-align: center; }
        .page-header h1 { font-size: 38px; margin-bottom: 10px; font-weight: 700; }

        /* --- SEARCH BAR --- */
        .search-wrapper {
            max-width: 600px;
            margin: -30px auto 40px auto; 
            position: relative;
            z-index: 10;
        }
        .search-box {
            display: flex;
            background: white;
            padding: 10px;
            border-radius: 50px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .search-box input {
            flex: 1;
            border: none;
            outline: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 50px;
        }
        .search-box button {
            background: #8e44ad;
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            transition: 0.3s;
        }
        .search-box button:hover { background: #732d91; transform: scale(1.05); }

        /* --- COURSE GRID --- */
        .container { max-width: 1400px; margin: 0 auto; padding: 0 20px 60px 20px; }
        
        /* UPDATED: Increased min-width from 320px to 380px for a larger feel */
        .course-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); 
            gap: 35px; 
            align-items: stretch;
        }
        
        .course-card { 
            background: white; 
            border-radius: 12px; 
            overflow: hidden; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.08); 
            transition: transform 0.3s ease; 
            display: flex; 
            flex-direction: column; 
            height: 100%; 
            position: relative; 
        }
        .course-card:hover { transform: translateY(-8px); }
        
        /* UPDATED: Increased height from 200px to 240px for larger images */
        .card-img { 
            width: 100%; 
            height: 240px; 
            object-fit: cover; 
            border-bottom: 1px solid #f0f0f0; 
        }

        .card-body { 
            padding: 30px; /* Increased padding for more "room" */
            flex-grow: 1; 
            display: flex; 
            flex-direction: column; 
        }

        .badge { display: inline-block; padding: 6px 14px; border-radius: 50px; font-size: 13px; font-weight: 600; margin-bottom: 15px; width: fit-content; }
        .badge.Beginner { background: #e8f5e9; color: #2e7d32; }
        .badge.Intermediate { background: #fff3e0; color: #ef6c00; }
        .badge.Advanced { background: #ffebee; color: #c62828; }

        /* UPDATED: Increased title size and set a min-height for alignment */
        .course-title { 
            font-size: 22px; 
            font-weight: 700; 
            color: #2c3e50; 
            margin-bottom: 12px; 
            min-height: 58px; /* Ensures buttons align even if title is 1 or 2 lines */
        }

        /* UPDATED: Increased font size for description */
        .course-desc { 
            font-size: 15px; 
            color: #666; 
            margin-bottom: 25px; 
            display: -webkit-box; 
            -webkit-line-clamp: 3; 
            -webkit-box-orient: vertical; 
            overflow: hidden; 
            flex-grow: 1; /* Pushes the button to the bottom */
            line-height: 1.6;
        }
        
        .btn-course { display: block; width: 100%; padding: 14px 0; text-align: center; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.3s; border: none; margin-top: auto; }
        .btn-enroll { background: transparent; border: 2px solid #8e44ad; color: #8e44ad; }
        .btn-enroll:hover { background: #8e44ad; color: white; }
        .btn-start { background: #27ae60; color: white; border: 2px solid #27ae60; }
        .btn-start:hover { background: #219150; }

        /* --- WISHLIST BUTTON STYLING --- */
        .wishlist-form {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 10;
        }
        .btn-wishlist {
            background: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }
        .btn-wishlist:hover { transform: scale(1.1); }

    </style>
</head>
<body>

    <nav class="navbar">
        <div class="logo">
            <a href="index.html"><img src="imgs/logo.jpg" alt="Smart Learn"></a>
        </div>
        
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="courses.php" class="active">Course</a></li>
            
            <li class="dropdown-parent">
                <a href="#">Notes <i class="fas fa-chevron-down"></i></a>
                <ul class="dropdown-menu">
                    <li class="dropdown-sub-parent">
                        <a href="#">Information Technology <i class="fas fa-chevron-right" style="float:right; font-size:12px; margin-top:4px;"></i></a>
                        <ul class="submenu">
                            <li><a href="view_notes.php?branch=IT&year=1st Year">1st Year</a></li>
                            <li><a href="view_notes.php?branch=IT&year=2nd Year">2nd Year</a></li>
                            <li><a href="view_notes.php?branch=IT&year=3rd Year">3rd Year</a></li>
                            <li><a href="view_notes.php?branch=IT&year=4th Year">4th Year</a></li>
                        </ul>
                    </li>
                    <li class="dropdown-sub-parent">
                        <a href="#">Computer Engineering <i class="fas fa-chevron-right" style="float:right; font-size:12px; margin-top:4px;"></i></a>
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
                        <a href="#">Information Technology <i class="fas fa-chevron-right" style="float:right; font-size:12px; margin-top:4px;"></i></a>
                        <ul class="submenu">
                            <li><a href="view_ebooks.php?branch=IT&year=1st Year">1st Year</a></li>
                            <li><a href="view_ebooks.php?branch=IT&year=2nd Year">2nd Year</a></li>
                            <li><a href="view_ebooks.php?branch=IT&year=3rd Year">3rd Year</a></li>
                            <li><a href="view_ebooks.php?branch=IT&year=4th Year">4th Year</a></li>
                        </ul>
                    </li>
                    <li class="dropdown-sub-parent">
                        <a href="#">Computer Engineering <i class="fas fa-chevron-right" style="float:right; font-size:12px; margin-top:4px;"></i></a>
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
        
        <?php if($user_id > 0): ?>
            <div class="user-profile dropdown-parent">
                <div class="avatar-circle">
                    <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                </div>
                <span class="profile-name">
                    <?php echo htmlspecialchars($user_name); ?> <i class="fas fa-chevron-down"></i>
                </span>
                <ul class="dropdown-menu profile-dropdown">
                    <li><a href="student-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="student_profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        <?php else: ?>
            <a href="login.php" class="btn-login">Login</a>
        <?php endif; ?>
    </nav>

    <header class="page-header">
        <h1>Education For The Real World</h1>
        <p>Master in-demand skills with our premium courses</p>
    </header>

    <div class="search-wrapper">
        <form action="courses.php" method="GET" class="search-box">
            <input type="text" name="search" placeholder="Search for a course..." value="<?php echo htmlspecialchars($search_term); ?>">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <div class="container">
        <div class="course-grid">
            
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="course-card">
                        
                        <?php 
                            // Check if this course is in the user's wishlist
                            $in_wishlist = in_array($row['id'], $wishlist_courses); 
                            $heart_class = $in_wishlist ? "fas fa-heart" : "far fa-heart"; 
                            $heart_color = $in_wishlist ? "#e74c3c" : "#aaa"; 
                        ?>
                        <form action="wishlist_process.php" method="POST" class="wishlist-form">
                            <input type="hidden" name="course_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="redirect" value="courses.php">
                            <button type="submit" class="btn-wishlist">
                                <i class="<?php echo $heart_class; ?>" style="color: <?php echo $heart_color; ?>; font-size: 20px;"></i>
                            </button>
                        </form>
                        <?php 
                            $img_path = !empty($row['thumbnail']) ? $row['thumbnail'] : 'imgs/default-course.jpg';
                            if (!file_exists($img_path) && strpos($img_path, 'http') === false) {
                                $img_path = 'https://via.placeholder.com/400x250?text=Course'; 
                            }
                        ?>
                        <img src="<?php echo $img_path; ?>" class="card-img" alt="Course Thumbnail">
                        
                        <div class="card-body">
                            <span class="badge <?php echo $row['level']; ?>"><?php echo $row['level']; ?></span>
                            
                            <h3 class="course-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                            
                            <p class="course-desc"><?php echo htmlspecialchars($row['description']); ?></p>
                            
                            <?php if ($user_id == 0): ?>
                                <button onclick="alert('Please Login to Enroll!'); window.location.href='login.php';" class="btn-course btn-enroll">Enroll Course</button>
                            
                            <?php elseif (in_array($row['id'], $enrolled_courses)): ?>
                                <a href="course_player.php?course_id=<?php echo $row['id']; ?>" class="btn-course btn-start">
                                    <i class="fas fa-play-circle"></i> Start Learning
                                </a>
                            
                            <?php else: ?>
                                <form action="enroll_process.php" method="POST">
                                    <input type="hidden" name="course_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn-course btn-enroll">Enroll Now</button>
                                </form>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #777;">
                    <h3>No courses found matching "<?php echo htmlspecialchars($search_term); ?>"</h3>
                    <a href="courses.php" style="color: #8e44ad; font-weight: bold;">Clear Search</a>
                </div>
            <?php endif; ?>

        </div>
    </div>

</body>
</html>
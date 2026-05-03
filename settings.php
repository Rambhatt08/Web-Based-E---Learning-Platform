<?php
session_start();
require 'db_connect.php';

// 1. SECURITY CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$message_type = "success"; 

// 2. HANDLE FORM SUBMISSIONS
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // --- A. IMAGE UPLOADS ---
    if (isset($_POST['upload_image_action'])) {
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        if (!empty($_FILES['profile_pic']['name'])) {
            $p_name = time() . "_" . basename($_FILES['profile_pic']['name']);
            if(move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_dir . $p_name)) {
                $conn->query("UPDATE users SET profile_pic = '$upload_dir$p_name' WHERE id = $user_id");
                $message = "Profile picture updated!";
            }
        }
        if (!empty($_FILES['cover_photo']['name'])) {
            $c_name = time() . "_cover_" . basename($_FILES['cover_photo']['name']);
            if(move_uploaded_file($_FILES['cover_photo']['tmp_name'], $upload_dir . $c_name)) {
                $conn->query("UPDATE users SET cover_photo = '$upload_dir$c_name' WHERE id = $user_id");
                $message = "Cover photo updated!";
            }
        }
    }

    // --- B. REMOVE IMAGES ---
    if (isset($_POST['remove_profile_pic'])) {
        $conn->query("UPDATE users SET profile_pic = NULL WHERE id = $user_id");
        $message = "Profile picture removed.";
    }
    if (isset($_POST['remove_cover_photo'])) {
        $conn->query("UPDATE users SET cover_photo = NULL WHERE id = $user_id");
        $message = "Cover photo removed.";
    }

    // --- C. UPDATE TEXT DETAILS (AND DISPLAY NAME) ---
    if (isset($_POST['update_profile_details'])) {
        $fname = $conn->real_escape_string($_POST['first_name']);
        $lname = $conn->real_escape_string($_POST['last_name']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $skill = $conn->real_escape_string($_POST['skill']);
        $bio = $conn->real_escape_string($_POST['bio']);
        $display_name_choice = $_POST['display_name']; // Get the dropdown value

        $sql = "UPDATE users SET First_Name='$fname', Last_Name='$lname', phone='$phone', skill='$skill', bio='$bio' WHERE id='$user_id'";
        
        if ($conn->query($sql)) {
            $message = "Profile details updated successfully!";
            
            // LOGIC: Update the Session Name immediately based on selection
            if ($display_name_choice === 'full_name') {
                $_SESSION['user_name'] = $fname . ' ' . $lname;
            } else {
                // If they chose 'username', fetch original username from DB
                $u_res = $conn->query("SELECT User_Name FROM users WHERE id=$user_id");
                $u_row = $u_res->fetch_assoc();
                $_SESSION['user_name'] = $u_row['User_Name'];
            }
        }
    }

    // --- D. CHANGE PASSWORD ---
    if (isset($_POST['change_password'])) {
        $current_pass = $_POST['current_pass'];
        $new_pass = $_POST['new_pass'];
        $confirm_pass = $_POST['confirm_pass'];

        $sql = "SELECT password FROM users WHERE id = $user_id";
        $res = $conn->query($sql);
        $row = $res->fetch_assoc();
        $stored_password = $row['password'];

        if (password_verify($current_pass, $stored_password) || $current_pass == $stored_password) {
            if ($new_pass === $confirm_pass) {
                $hashed_new_pass = password_hash($new_pass, PASSWORD_DEFAULT);
                $conn->query("UPDATE users SET password = '$hashed_new_pass' WHERE id = $user_id");
                $message = "Password changed successfully!";
            } else {
                $message = "New passwords do not match!";
                $message_type = "error";
            }
        } else {
            $message = "Current password is incorrect!";
            $message_type = "error";
        }
    }

    // --- E. UPDATE SOCIAL LINKS ---
    if (isset($_POST['update_social'])) {
        $fb = $conn->real_escape_string($_POST['facebook']);
        $tw = $conn->real_escape_string($_POST['twitter']);
        $li = $conn->real_escape_string($_POST['linkedin']);
        $wb = $conn->real_escape_string($_POST['website']);
        $gh = $conn->real_escape_string($_POST['github']);

        $conn->query("UPDATE users SET facebook='$fb', twitter='$tw', linkedin='$li', website='$wb', github='$gh' WHERE id='$user_id'");
        $message = "Social profiles updated!";
    }
}

// 3. FETCH USER DATA & SET VARIABLES
// Crucial: We set $user_name AFTER the POST logic so it reflects updates immediately
$user_name = $_SESSION['user_name']; 

$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// --- SMART DEFAULTS ---
$is_default_pic = empty($user['profile_pic']);
$profile_pic = $is_default_pic ? 'imgs/default-user.png' : $user['profile_pic'];

$is_default_cover = empty($user['cover_photo']);
$cover_photo_url = $is_default_cover ? 'imgs/default-cover.jpg' : $user['cover_photo'];

// Determine which option to select in the dropdown
$is_fullname_selected = ($user_name === ($user['First_Name'] . ' ' . $user['Last_Name']));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings - Smart Learn</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="imgs/favicon.png" type="image/png">

    <style>
        /* (CSS REMAINS THE SAME) */
        .settings-container { background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .settings-tabs { display: flex; gap: 30px; border-bottom: 1px solid #ddd; margin-bottom: 30px; }
        .tab-btn { background: none; border: none; padding: 10px 0; cursor: pointer; color: #777; font-weight: 500; font-size: 16px; border-bottom: 2px solid transparent; font-family: 'Poppins', sans-serif; }
        .tab-btn.active { color: #3b82f6; border-bottom: 2px solid #3b82f6; }
        .tab-content { display: none; animation: fadeIn 0.3s; }
        .tab-content.active-content { display: block; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .cover-section {
            height: 220px; background-size: cover; background-position: center; border-radius: 10px; position: relative; margin-bottom: 50px; background-color: #e0e0e0;
            <?php if($is_default_cover): ?> background: linear-gradient(135deg, #a8c0ff 0%, #3f2b96 100%); <?php endif; ?>
        }
        .cover-controls { position: absolute; bottom: 15px; right: 15px; display: flex; gap: 8px; }
        .btn-cover-action { background: rgba(0,0,0,0.6); color: white; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size: 13px; border: none; display: flex; align-items: center; gap: 5px; transition: 0.2s; }
        .btn-cover-action:hover { background: rgba(0,0,0,0.8); }
        .btn-cover-delete { background: rgba(231, 76, 60, 0.8); }
        .btn-cover-delete:hover { background: #c0392b; }

        .profile-pic-container { position: absolute; bottom: -50px; left: 40px; width: 140px; height: 140px; border-radius: 50%; border: 5px solid white; background: white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .profile-img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
        
        .camera-overlay {
            position: absolute; bottom: 0; left: 0; width: 100%; height: 40%; background: rgba(0, 0, 0, 0.5); border-bottom-left-radius: 100px; border-bottom-right-radius: 100px; display: flex; align-items: center; justify-content: center; color: white; opacity: 0; transition: opacity 0.3s; cursor: pointer;
        }
        .profile-pic-container:hover .camera-overlay { opacity: 1; }

        .profile-menu {
            position: absolute; top: 90%; left: 50%; transform: translateX(-50%); background: #333; color: white; width: 160px; border-radius: 6px; padding: 5px 0; display: none; z-index: 100; box-shadow: 0 5px 15px rgba(0,0,0,0.2); padding-top: 10px; background-clip: content-box;
        }
        .profile-menu::before { content: ""; position: absolute; top: -20px; left: 0; width: 100%; height: 30px; background: transparent; }
        .profile-menu::after { content: ""; position: absolute; bottom: 100%; left: 50%; margin-left: -5px; border-width: 5px; border-style: solid; border-color: transparent transparent #333 transparent; margin-bottom: -10px; }
        .profile-pic-container:hover .profile-menu { display: block; }

        .menu-item { display: flex; align-items: center; gap: 10px; padding: 10px 15px; font-size: 13px; color: white; cursor: pointer; transition: 0.2s; text-decoration: none; width: 100%; border: none; background: none; text-align: left; }
        .menu-item:hover { background: #444; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .form-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 15px; }
        .form-group label { font-weight: 600; font-size: 14px; color: #333; }
        .form-group input, .form-group select, .form-group textarea { padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Poppins', sans-serif; }
        .form-group input[readonly] { background-color: #f9f9f9; color: #777; }
        .full-width { grid-column: 1 / -1; }
        .btn-update { background: #3b82f6; color: white; border: none; padding: 12px 25px; border-radius: 6px; font-weight: 600; cursor: pointer; margin-top: 10px; }
        .btn-update:hover { background: #2563eb; }
        .alert { padding: 10px; border-radius: 6px; margin-bottom: 20px; }
        .alert.success { background: #d4edda; color: #155724; }
        .alert.error { background: #f8d7da; color: #721c24; }
        input[type="file"] { display: none; }
    </style>
</head>
<body>

<nav class="navbar dashboard-nav">
    <div class="logo"><a href="index.html"><img src="imgs/logo.jpg" alt="Logo"></a></div>
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
      
            <li><a href="question-answer.php"><i class="fas fa-question-circle"></i> Question & Answer</a></li>
            <li><a href="settings.php" class="active"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="dash-content">
        <h3>Settings</h3>

        <div class="settings-container">
            <div class="settings-tabs">
                <button class="tab-btn active" onclick="openTab(event, 'profile')">Profile</button>
                <button class="tab-btn" onclick="openTab(event, 'password')">Password</button>
                <button class="tab-btn" onclick="openTab(event, 'social')">Social Profile</button>
            </div>

            <?php if($message): ?>
                <div class="alert <?php echo $message_type; ?>"><?php echo $message; ?></div>
            <?php endif; ?>

            <div id="profile" class="tab-content active-content">
                
                <div class="cover-section" style="background-image: url('<?php echo $cover_photo_url; ?>');">
                    <div class="cover-controls">
                        <form action="settings.php" method="POST" enctype="multipart/form-data" id="coverForm" style="margin:0;">
                            <input type="hidden" name="upload_image_action" value="1">
                            <label for="cover_upload" class="btn-cover-action"><i class="fas fa-camera"></i> Upload Cover</label>
                            <input type="file" id="cover_upload" name="cover_photo" onchange="document.getElementById('coverForm').submit()">
                        </form>
                        
                        <?php if(!$is_default_cover): ?>
                        <form action="settings.php" method="POST" style="margin:0;">
                            <input type="hidden" name="remove_cover_photo" value="1">
                            <button type="submit" class="btn-cover-action btn-cover-delete" onclick="return confirm('Remove cover photo?')"><i class="fas fa-trash"></i></button>
                        </form>
                        <?php endif; ?>
                    </div>

                    <div class="profile-pic-container">
                        <img src="<?php echo $profile_pic; ?>" alt="Profile" class="profile-img">
                        <div class="camera-overlay"><i class="fas fa-camera" style="font-size: 20px;"></i></div>

                        <div class="profile-menu">
                            <form action="settings.php" method="POST" enctype="multipart/form-data" id="profileForm" style="margin:0;">
                                <input type="hidden" name="upload_image_action" value="1">
                                <label for="profile_upload" class="menu-item">
                                    <i class="fas fa-image"></i> Upload Photo
                                </label>
                                <input type="file" id="profile_upload" name="profile_pic" onchange="document.getElementById('profileForm').submit()">
                            </form>

                            <?php if(!$is_default_pic): ?>
                            <form action="settings.php" method="POST" style="margin:0;">
                                <input type="hidden" name="remove_profile_pic" value="1">
                                <button type="submit" class="menu-item" onclick="return confirm('Remove profile picture?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <form action="settings.php" method="POST">
                    <input type="hidden" name="update_profile_details" value="1">
                    <div class="form-grid">
                        <div class="form-group"><label>First Name</label><input type="text" name="first_name" value="<?php echo htmlspecialchars($user['First_Name']); ?>"></div>
                        <div class="form-group"><label>Last Name</label><input type="text" name="last_name" value="<?php echo htmlspecialchars($user['Last_Name']); ?>"></div>
                        <div class="form-group"><label>User Name</label><input type="text" value="<?php echo htmlspecialchars($user['User_Name']); ?>" readonly></div>
                        <div class="form-group"><label>Phone Number</label><input type="text" name="phone" value="<?php echo isset($user['phone']) ? htmlspecialchars($user['phone']) : ''; ?>"></div>
                        <div class="form-group full-width"><label>Skill/Occupation</label><input type="text" name="skill" value="<?php echo isset($user['skill']) ? htmlspecialchars($user['skill']) : ''; ?>"></div>
                        <div class="form-group full-width"><label>Bio</label><textarea name="bio" rows="4"><?php echo isset($user['bio']) ? htmlspecialchars($user['bio']) : ''; ?></textarea></div>
                        
                        <div class="form-group full-width">
                            <label>Display name publicly as</label>
                            <select name="display_name">
                                <option value="username" <?php echo !$is_fullname_selected ? 'selected' : ''; ?>>User Name</option>
                                <option value="full_name" <?php echo $is_fullname_selected ? 'selected' : ''; ?>>Full Name</option>
                            </select>
                            <small style="color:#777; margin-top:5px;">The display name is shown in all public fields.</small>
                        </div>
                    </div>
                    <button type="submit" class="btn-update">Update Profile</button>
                </form>
            </div>

            <div id="password" class="tab-content">
               <form action="settings.php" method="POST">
                    <input type="hidden" name="change_password" value="1">
                    <div class="form-group full-width"><label>Current Password</label><input type="password" name="current_pass" required></div>
                    <div class="form-group full-width"><label>New Password</label><input type="password" name="new_pass" required></div>
                    <div class="form-group full-width"><label>Re-type New Password</label><input type="password" name="confirm_pass" required></div>
                    <button type="submit" class="btn-update">Reset Password</button>
                </form>
            </div>

            <div id="social" class="tab-content">
                <form action="settings.php" method="POST">
                    <input type="hidden" name="update_social" value="1">
                    <div class="form-group full-width"><label>Facebook</label><div class="social-input-group"><i class="fab fa-facebook-f"></i><input type="text" name="facebook" value="<?php echo isset($user['facebook']) ? htmlspecialchars($user['facebook']) : ''; ?>"></div></div>
                    <div class="form-group full-width"><label>X (Twitter)</label><div class="social-input-group"><i class="fab fa-twitter"></i><input type="text" name="twitter" value="<?php echo isset($user['twitter']) ? htmlspecialchars($user['twitter']) : ''; ?>"></div></div>
                    <div class="form-group full-width"><label>LinkedIn</label><div class="social-input-group"><i class="fab fa-linkedin-in"></i><input type="text" name="linkedin" value="<?php echo isset($user['linkedin']) ? htmlspecialchars($user['linkedin']) : ''; ?>"></div></div>
                    <div class="form-group full-width"><label>Website</label><div class="social-input-group"><i class="fas fa-globe"></i><input type="text" name="website" value="<?php echo isset($user['website']) ? htmlspecialchars($user['website']) : ''; ?>"></div></div>
                    <div class="form-group full-width"><label>Github</label><div class="social-input-group"><i class="fab fa-github"></i><input type="text" name="github" value="<?php echo isset($user['github']) ? htmlspecialchars($user['github']) : ''; ?>"></div></div>
                    <button type="submit" class="btn-update">Update Socials</button>
                </form>
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
        for (i = 0; i < tabcontent.length; i++) { tabcontent[i].style.display = "none"; }
        tablinks = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tablinks.length; i++) { tablinks[i].className = tablinks[i].className.replace(" active", ""); }
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }
</script>

</body>
</html>
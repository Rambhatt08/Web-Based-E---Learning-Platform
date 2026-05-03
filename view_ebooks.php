<?php
session_start();

// 1. Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Database Connection
$host = "localhost";
$username = "root";
$password = "";
$dbname = "smartlearn_db";
$conn = mysqli_connect($host, $username, $password, $dbname);

// 3. Get the Filter Info
$branch_code = isset($_GET['branch']) ? $_GET['branch'] : '';
$year_level = isset($_GET['year']) ? $_GET['year'] : '';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : ''; 

// Map codes to full names for the Banner
$branch_name = "All Branches";
if ($branch_code == 'IT') $branch_name = "Information Technology";
if ($branch_code == 'CE') $branch_name = "Computer Engineering";

// 4. Build SQL (Querying 'ebooks' table now)
$sql = "SELECT * FROM ebooks WHERE branch = '$branch_code' AND year_level = '$year_level'";

if (!empty($search_query)) {
    $safe_search = mysqli_real_escape_string($conn, $search_query);
    $sql .= " AND title LIKE '%$safe_search%'";
}

$sql .= " ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Books: <?php echo $branch_code; ?> - Smart Learn</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    
    <style>
        /* --- COPYING YOUR EXACT STYLES FROM view_notes.php --- */
        .search-container {
            margin: 20px 0 20px 30px; 
            max-width: 500px; 
        }

        .search-box {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 50px;
            padding: 5px; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border: 1px solid #ddd;
        }

        .search-input {
            border: none;
            outline: none;
            flex: 1;
            padding: 12px 20px;
            font-size: 15px;
            border-radius: 50px;
            font-family: 'Poppins', sans-serif;
            color: #555;
        }

        .search-btn {
            background: #3498db;
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            transition: background 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 2px;
            padding: 0;
        }

        .search-btn:hover { background: #2980b9; }

        .search-btn svg {
            width: 20px;
            height: 20px;
            stroke: white;
            stroke-width: 2.5;
            fill: none;    
        }

        /* --- HERO THEME --- */
        .hero-header {
            background: linear-gradient(135deg, #a4508b 0%, #5f0a87 74%);
            color: white;
            padding: 40px 20px;
            text-align: center;
            margin-bottom: 0; 
            border-radius: 0 0 15px 15px;
            box-shadow: 0 4px 15px rgba(155, 89, 182, 0.4);
        }

        .hero-title {
            font-size: 42px;
            font-weight: 700;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .hero-subtitle {
            font-size: 16px;
            margin-top: 10px;
            opacity: 0.9;
            font-weight: 400;
        }

        /* --- CARD GRID --- */
        .notes-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
            gap: 30px; 
            margin-top: 20px; 
            padding-bottom: 50px;
            padding-left: 30px; 
            padding-right: 30px;
        }

        .note-card { 
            background: white; 
            border-radius: 12px; 
            overflow: hidden; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.08); 
            transition: transform 0.3s ease; 
            display: flex; 
            flex-direction: column; 
            min-height: 380px; 
            border: 1px solid #eee;
        }

        .note-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }

        .card-img-wrapper {
            width: 100%;
            height: 220px; /* Made slightly taller for Book Covers */
            overflow: hidden;
            background-color: #f9f9f9;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-img { 
            width: 100%; 
            height: 100%; 
            object-fit: contain; /* Ensures the whole book cover is visible */
            transition: transform 0.5s;
            padding: 10px; /* Add padding so book doesn't touch edges */
        }
        
        .note-card:hover .card-img { transform: scale(1.05); }

        .card-body { 
            padding: 20px; 
            flex-grow: 1; 
            display: flex; 
            flex-direction: column; 
        }

        .card-title { 
            font-size: 18px; 
            font-weight: 600; 
            margin: 0 0 10px; 
            color: #2c3e50; 
            line-height: 1.4;
        }

        .card-subject { 
            font-size: 14px; 
            color: #7f8c8d; 
            margin-bottom: 20px; 
        }

        /* Green Button for Download */
        .btn-read { 
            display: inline-block; 
            width: 100%; 
            padding: 12px 0; 
            background: #27ae60; /* Green for Download */
            color: white; 
            text-align: center; 
            border-radius: 6px; 
            text-decoration: none; 
            margin-top: auto; 
            font-weight: 500;
            transition: background 0.3s;
        }

        .btn-read:hover { background: #219150; }
        .no-notes { text-align: center; width: 100%; color: #777; font-size: 18px; margin-top: 40px; }
        .dash-content { padding: 0 !important; }
    </style>
</head>
<body>

<nav class="navbar dashboard-nav">
    <div class="logo">
        <a href="student-dashboard.php"><img src="imgs/logo.jpg" alt="Smart Learn Logo"></a>
    </div>
    
    <ul class="nav-links">
        <li><a href="student-dashboard.php">Home</a></li>
        
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

        <li><a href="student-dashboard.php">Dashboard</a></li>
    </ul>
    
    <div class="user-profile dropdown-parent">
        <div class="profile-trigger">
            <div class="avatar-circle">
                <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
            </div>
            <span><?php echo htmlspecialchars($_SESSION['user_name']); ?> <i class="fas fa-chevron-down"></i></span>
        </div>
        <ul class="dropdown-menu profile-dropdown">
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="dashboard-container">
    
    <aside class="sidebar">
        <ul>
            <li><a href="student-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="view_notes.php"><i class="fas fa-file-alt"></i> Notes</a></li>
            <li><a href="#" class="active"><i class="fas fa-book"></i> E-Books</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="dash-content">
        
        <div class="hero-header">
             <?php 
                if($branch_code) {
                    echo '<h1 class="hero-title">' . strtoupper($year_level) . ' (E-BOOKS)</h1>';
                    echo '<p class="hero-subtitle">Make learning effective - ' . $branch_name . '</p>';
                } else {
                    echo '<h1 class="hero-title">DIGITAL LIBRARY</h1>';
                    echo '<p class="hero-subtitle">Select a Branch from the E-Book Menu</p>';
                }
             ?>
        </div>

        <div class="search-container">
            <form action="view_ebooks.php" method="GET">
                <div class="search-box">
                    <input type="hidden" name="branch" value="<?php echo htmlspecialchars($branch_code); ?>">
                    <input type="hidden" name="year" value="<?php echo htmlspecialchars($year_level); ?>">
                    
                    <input type="text" name="search" class="search-input" 
                           placeholder="Search e-books (e.g. Java)..." 
                           value="<?php echo htmlspecialchars($search_query); ?>">
                    
                    <button type="submit" class="search-btn">
                        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                             <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="notes-grid">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Use 'cover_image' from ebooks table, fallback to default if missing
                    $thumb_path = !empty($row['cover_image']) ? $row['cover_image'] : 'uploads/thumbnails/default.png';
                    $file_link = $row['file_path'];

                    echo '
                    <div class="note-card">
                        <div class="card-img-wrapper">
                            <img src="'.$thumb_path.'" class="card-img" alt="Book Cover">
                        </div>
                        <div class="card-body">
                            <h4 class="card-title">'.$row['title'].'</h4>
                            
                            <p class="card-subject"><i class="fas fa-graduation-cap"></i> '.$row['branch'].' - '.$row['year_level'].'</p>
                            
                            <a href="'.$file_link.'" download class="btn-read">
                                <i class="fas fa-download"></i> Download E-Book
                            </a>
                        </div>
                    </div>';
                }
            } else {
                if(!empty($search_query)) {
                    echo '<div class="no-notes">
                        <i class="fas fa-search" style="font-size: 50px; margin-bottom: 20px; color: #ccc;"></i><br>
                        No results found for "<b>' . htmlspecialchars($search_query) . '</b>"
                      </div>';
                } elseif($branch_code) {
                    echo '<div class="no-notes">
                        <i class="fas fa-book-open" style="font-size: 50px; margin-bottom: 20px; color: #ccc;"></i><br>
                        No E-Books uploaded yet for this year.
                      </div>';
                } else {
                    echo '<p style="text-align:center;">Select Branch and Year from the "E-Book" menu above.</p>';
                }
            }
            ?>
        </div>
    </main>
</div>

</body>
</html>
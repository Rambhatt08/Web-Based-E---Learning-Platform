<?php
session_start();
require 'db_connect.php';

// 1. GET THE CLICKED SUBJECT FROM THE URL
// Example URL: subject_notes.php?subject=Engineering Maths 1&year=1st Year&branch=Computer Engineering
$subject = isset($_GET['subject']) ? $_GET['subject'] : '';
$year    = isset($_GET['year']) ? $_GET['year'] : '';
$branch  = isset($_GET['branch']) ? $_GET['branch'] : '';

// If no subject selected, redirect back
if(empty($subject)){
    header("Location: student-dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $subject; ?> - Notes</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* HEADER STYLE (Matches your Purple Screenshot) */
        .subject-header {
            background: linear-gradient(135deg, #8E2DE2, #4A00E0); /* Purple Gradient */
            color: white;
            padding: 60px 20px;
            text-align: center;
        }
        .subject-header h1 { margin: 0; font-size: 36px; }

        /* GRID LAYOUT */
        .notes-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }

        /* CARD STYLE */
        .note-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            text-align: center;
            padding: 30px 20px;
            transition: transform 0.3s ease;
            border: 1px solid #eee;
        }
        .note-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        /* ICON & TEXT */
        .pdf-icon {
            font-size: 50px;
            color: #dc3545; /* Red PDF color */
            margin-bottom: 15px;
        }
        .note-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            min-height: 50px; /* Aligns buttons evenly */
        }

        /* BUTTONS */
        .btn-download {
            display: inline-block;
            text-decoration: none;
            background: white;
            color: #333;
            border: 1px solid #ccc;
            padding: 8px 20px;
            border-radius: 5px;
            font-size: 14px;
            transition: 0.3s;
        }
        .btn-download:hover {
            background: #f8f9fa;
            border-color: #aaa;
        }
    </style>
</head>
<body>

    <nav style="padding: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <a href="student-dashboard.php" style="text-decoration:none; font-weight:bold; color:#333;">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </nav>

    <div class="subject-header">
        <h1><?php echo htmlspecialchars($subject); ?></h1>
        <p style="opacity: 0.8; margin-top: 10px;">
            <?php echo htmlspecialchars($branch); ?> &bull; <?php echo htmlspecialchars($year); ?>
        </p>
    </div>

    <div class="notes-container">

        <?php
        // 2. FETCH NOTES FROM DATABASE
        // We assume your table is named 'notes'
        $stmt = $conn->prepare("SELECT * FROM notes WHERE subject = ? ORDER BY id DESC");
        $stmt->bind_param("s", $subject);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Get file path (e.g., uploads/notes/123_math.pdf)
                $file_path = htmlspecialchars($row['file_path']);
                $title = htmlspecialchars($row['title']);
                
                // GENERATE CARD HTML
                echo '
                <div class="note-card">
                    <i class="fas fa-file-pdf pdf-icon"></i>
                    <div class="note-title">'.$title.'</div>
                    <a href="'.$file_path.'" target="_blank" class="btn-download">
                        <i class="fas fa-download"></i> View / Download
                    </a>
                </div>
                ';
            }
        } else {
            echo '<p style="text-align:center; width:100%; color:#666; font-size:18px;">No notes uploaded for this subject yet.</p>';
        }
        ?>

    </div>

</body>
</html>
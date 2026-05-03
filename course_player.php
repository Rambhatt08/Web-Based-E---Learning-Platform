<?php
session_start();
require 'db_connect.php';

// 1. SECURITY
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. GET COURSE ID
if (!isset($_GET['course_id'])) {
    echo "No course selected.";
    exit();
}
$course_id = intval($_GET['course_id']);

// 3. CHECK ENROLLMENT
$check_enroll = $conn->query("SELECT * FROM enrollments WHERE user_id = $user_id AND course_id = $course_id");
if ($check_enroll->num_rows == 0) {
    echo "<script>alert('You must enroll in this course first!'); window.location.href='courses.php';</script>";
    exit();
}

// 4. FETCH DETAILS
$course = $conn->query("SELECT * FROM courses WHERE id = $course_id")->fetch_assoc();

// 5. FETCH LECTURES
$lectures_query = $conn->query("SELECT * FROM course_lectures WHERE course_id = $course_id ORDER BY lecture_order ASC");
$lectures = [];
while ($row = $lectures_query->fetch_assoc()) {
    $lectures[] = $row;
}

// 6. DETERMINE CURRENT VIDEO
$current_video = null;
if (isset($_GET['lecture_id'])) {
    foreach ($lectures as $lec) {
        if ($lec['id'] == $_GET['lecture_id']) {
            $current_video = $lec;
            break;
        }
    }
} else {
    if (count($lectures) > 0) {
        $current_video = $lectures[0];
    }
}

// 7. FETCH PROGRESS (NEW LOGIC)
$prog_query = $conn->query("SELECT video_id FROM video_progress WHERE user_id = $user_id AND course_id = $course_id AND is_completed = 1");
$completed_ids = [];
while($p = $prog_query->fetch_assoc()){
    $completed_ids[] = $p['video_id'];
}

// --- UPDATED HELPER: DETECT VIDEO TYPE & PREPARE URL ---
$video_type = 'none'; 
$final_url = '';

if ($current_video) {
    $raw_url = $current_video['video_url'];
    
    // IMPROVED DETECTION: If 'uploads' is found anywhere in the path, it is a local file
    if (stripos($raw_url, 'uploads') !== false) {
        $video_type = 'local';
        $final_url = $raw_url;
    } 
    // Otherwise assume YouTube
    else {
        $video_type = 'youtube';
        if (strpos($raw_url, 'youtu.be') !== false) {
            $parts = explode('/', parse_url($raw_url, PHP_URL_PATH));
            $videoId = end($parts);
            $final_url = 'https://www.youtube.com/embed/' . $videoId . '?enablejsapi=1&rel=0';
        } elseif (strpos($raw_url, 'watch?v=') !== false) {
            parse_str(parse_url($raw_url, PHP_URL_QUERY), $params);
            if (isset($params['v'])) {
                $final_url = 'https://www.youtube.com/embed/' . $params['v'] . '?enablejsapi=1&rel=0';
            }
        } else {
            $final_url = $raw_url . (strpos($raw_url, '?') !== false ? '&' : '?') . 'enablejsapi=1';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($course['title']); ?> - Smart Learn</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { margin: 0; font-family: 'Poppins', sans-serif; background-color: #f0f2f5; display: flex; flex-direction: column; height: 100vh; }
        .player-nav { background: #2c3e50; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        .back-btn { color: white; text-decoration: none; font-size: 14px; background: rgba(255,255,255,0.1); padding: 8px 15px; border-radius: 4px; }
        .main-container { display: flex; flex: 1; height: calc(100vh - 60px); }
        .video-section { flex: 3; background: #000; display: flex; flex-direction: column; overflow-y: auto; }
        .video-wrapper { position: relative; padding-bottom: 56.25%; height: 0; background: black; display: flex; align-items: center; justify-content: center; }
        .video-wrapper iframe, .video-wrapper video { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; }
        .video-info { padding: 20px; background: white; }
        .video-title { margin: 0 0 10px; font-size: 24px; color: #333; }
        .video-desc { color: #666; font-size: 14px; }
        .playlist-section { flex: 1; background: white; border-left: 1px solid #ddd; overflow-y: auto; max-width: 350px; display: flex; flex-direction: column; }
        .playlist-header { padding: 15px; background: #f8f9fa; border-bottom: 1px solid #ddd; font-weight: 600; color: #333; }
        .playlist-item { display: flex; padding: 15px; border-bottom: 1px solid #eee; text-decoration: none; color: #555; transition: 0.2s; align-items: center; }
        .playlist-item:hover { background-color: #f1f1f1; }
        .playlist-item.active { background-color: #e3f2fd; border-left: 4px solid #2196f3; color: #1976d2; }
        .icon-box { margin-right: 10px; width: 20px; text-align: center; }
        .lecture-title { font-size: 14px; font-weight: 500; }
        .lecture-duration { margin-left: auto; font-size: 12px; color: #999; }
        .playlist-item.completed .icon-box { color: #2ecc71; }
    </style>
</head>
<body>

    <div class="player-nav">
        <a href="courses.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Courses</a>
        <h2 style="margin:0; font-size:18px;"><?php echo htmlspecialchars($course['title']); ?></h2>
        <div></div>
    </div>

    <div class="main-container">
        <div class="video-section">
            <?php if ($current_video): ?>
                <div class="video-wrapper">
                    <?php if ($video_type == 'youtube'): ?>
                        <iframe id="yt-player" src="<?php echo $final_url; ?>" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                    <?php elseif ($video_type == 'local'): ?>
                        <video id="local-player" controls autoplay>
                            <source src="<?php echo $final_url; ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    <?php else: ?>
                        <p style="color:white; padding:20px;">Invalid Video Source</p>
                    <?php endif; ?>
                </div>
                <div class="video-info">
                    <h1 class="video-title"><?php echo htmlspecialchars($current_video['title']); ?></h1>
                    <p class="video-desc">Now playing: Lecture #<?php echo $current_video['lecture_order']; ?></p>
                </div>
            <?php else: ?>
                <div style="padding:50px; color:white; text-align:center;">
                    <h2>No videos available.</h2>
                </div>
            <?php endif; ?>
        </div>

        <div class="playlist-section">
            <div class="playlist-header">Course Content</div>
            <?php if (count($lectures) > 0): ?>
                <?php foreach ($lectures as $lec): 
                    $isActive = ($current_video && $current_video['id'] == $lec['id']) ? 'active' : '';
                    $isCompleted = in_array($lec['id'], $completed_ids); 
                    $itemClass = $isActive . ($isCompleted ? ' completed' : '');
                ?>
                    <a href="course_player.php?course_id=<?php echo $course_id; ?>&lecture_id=<?php echo $lec['id']; ?>" class="playlist-item <?php echo $itemClass; ?>" id="lec-<?php echo $lec['id']; ?>">
                        <div class="icon-box">
                            <?php if($isCompleted): ?>
                                <i class="fas fa-check-circle"></i>
                            <?php else: ?>
                                <i class="fas <?php echo ($isActive ? 'fa-play-circle' : 'fa-play'); ?>"></i>
                            <?php endif; ?>
                        </div>
                        <span class="lecture-title"><?php echo htmlspecialchars($lec['title']); ?></span>
                        <span class="lecture-duration"><?php echo htmlspecialchars($lec['duration']); ?></span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

<script>
    const currentCourseId = <?php echo $course_id; ?>;
    const currentVideoId  = <?php echo $current_video ? $current_video['id'] : 0; ?>;
    const videoType       = '<?php echo $video_type; ?>';

    function sendCompletionToDB() {
        console.log("Attempting to mark video " + currentVideoId + " as complete...");
        let data = new FormData();
        data.append('course_id', currentCourseId);
        data.append('video_id', currentVideoId);

        fetch('update_progress.php', {
            method: 'POST',
            body: data
        })
        .then(res => res.text())
        .then(response => {
            console.log("Server says: ", response);
            if (response.includes("SUCCESS_UPDATED")) {
                updateUI();
            }
        })
        .catch(err => console.error("Network Error: ", err));
    }

    function updateUI() {
        const activeItem = document.querySelector('.playlist-item.active');
        if (activeItem) {
            activeItem.classList.add('completed');
            const iconBox = activeItem.querySelector('.icon-box');
            if (iconBox) iconBox.innerHTML = '<i class="fas fa-check-circle" style="color: #2ecc71;"></i>';
        }
    }

    if (videoType === 'local') {
        const localPlayer = document.getElementById('local-player');
        if (localPlayer) {
            localPlayer.onended = () => sendCompletionToDB();
        }
    } else if (videoType === 'youtube') {
        if (typeof YT === 'undefined') {
            let tag = document.createElement('script');
            tag.src = "https://www.youtube.com/iframe_api";
            document.head.appendChild(tag);
        }

        window.onYouTubeIframeAPIReady = function() {
            new YT.Player('yt-player', {
                events: {
                    'onStateChange': function(event) {
                        if (event.data === 0) sendCompletionToDB();
                    }
                }
            });
        };
    }
</script>

</body>
</html>
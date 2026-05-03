<?php
session_start();
require 'db_connect.php';

// SECURITY: Strict check for Admin Role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 1. Get the attempt ID from URL
if (!isset($_GET['attempt_id'])) {
    die("No attempt ID provided.");
}
$attempt_id = intval($_GET['attempt_id']);

// 2. Fetch header info (Student, Quiz, Course, and Score)
$info_sql = "SELECT qa.*, u.User_Name, q.title as quiz_title, c.title as course_title 
             FROM quiz_attempts qa
             JOIN users u ON qa.user_id = u.id
             JOIN quizzes q ON qa.quiz_id = q.id
             JOIN courses c ON q.course_id = c.id
             WHERE qa.id = $attempt_id";
$info_res = $conn->query($info_sql);

if ($info_res->num_rows == 0) { die("Attempt not found."); }
$info = $info_res->fetch_assoc();

// 3. Fetch all specific question responses for this attempt
$responses_sql = "SELECT qr.*, q.question_text, q.option_a, q.option_b, q.option_c, q.option_d, q.correct_option 
                  FROM quiz_responses qr
                  JOIN quiz_questions q ON qr.question_id = q.id
                  WHERE qr.attempt_id = $attempt_id";
$responses = $conn->query($responses_sql);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quiz Attempt Details - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; padding: 40px; color: #333; }
        .container { max-width: 900px; margin: auto; background: white; padding: 35px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        
        .btn-back { display: inline-block; margin-bottom: 25px; color: #3498db; text-decoration: none; font-weight: 600; font-size: 14px; transition: 0.3s; }
        .btn-back:hover { color: #2980b9; transform: translateX(-5px); }

        .header-section { border-bottom: 2px solid #f0f0f0; padding-bottom: 20px; margin-bottom: 30px; }
        .header-section h2 { margin: 0; color: #2c3e50; font-size: 24px; }
        .header-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px; }
        .header-item { font-size: 14px; color: #666; }
        .header-item strong { color: #333; }

        .question-box { background: #fff; padding: 25px; border-radius: 10px; border: 1px solid #eee; margin-bottom: 25px; position: relative; }
        .question-box.correct-border { border-left: 5px solid #2ecc71; }
        .question-box.wrong-border { border-left: 5px solid #e74c3c; }

        .status-badge { position: absolute; top: 20px; right: 20px; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .status-correct { background: #d4edda; color: #155724; }
        .status-wrong { background: #f8d7da; color: #721c24; }

        .question-text { font-size: 17px; font-weight: 600; margin-bottom: 15px; display: block; color: #2c3e50; }
        
        .option-row { padding: 12px 15px; margin: 8px 0; border-radius: 6px; font-size: 14px; background: #f9f9f9; border: 1px solid #eee; display: flex; justify-content: space-between; }
        
        /* Highlighting Logic */
        .opt-selected-wrong { background: #fff5f5; border-color: #feb2b2; color: #c53030; }
        .opt-is-correct { background: #f0fff4; border-color: #9ae6b4; color: #22543d; font-weight: 600; }
        
        .label-pill { font-size: 10px; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; margin-left: 10px; }
        .pill-user { background: #3b82f6; color: white; }
        .pill-correct { background: #2ecc71; color: white; }
    </style>
</head>
<body>

<div class="container">
    <a href="admin_quiz_results.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to All Results</a>
    
    <div class="header-section">
        <h2>Attempt Analysis</h2>
        <div class="header-grid">
            <div class="header-item">Student: <strong><?php echo htmlspecialchars($info['User_Name']); ?></strong></div>
            <div class="header-item">Quiz: <strong><?php echo htmlspecialchars($info['quiz_title']); ?></strong></div>
            <div class="header-item">Course: <strong><?php echo htmlspecialchars($info['course_title']); ?></strong></div>
            <div class="header-item">Final Score: <strong style="color: #2ecc71; font-size: 18px;"><?php echo $info['score']; ?> / <?php echo $info['total_questions']; ?></strong></div>
        </div>
    </div>

    <?php if ($responses->num_rows > 0): ?>
        <?php $count = 1; while($row = $responses->fetch_assoc()): ?>
            <div class="question-box <?php echo $row['is_correct'] ? 'correct-border' : 'wrong-border'; ?>">
                <span class="status-badge <?php echo $row['is_correct'] ? 'status-correct' : 'status-wrong'; ?>">
                    <?php echo $row['is_correct'] ? '<i class="fas fa-check"></i> Correct' : '<i class="fas fa-times"></i> Incorrect'; ?>
                </span>
                
                <span class="question-text">Question <?php echo $count++; ?>: <?php echo htmlspecialchars($row['question_text']); ?></span>
                
                <?php 
                    $options = [
                        'A' => $row['option_a'], 
                        'B' => $row['option_b'], 
                        'C' => $row['option_c'], 
                        'D' => $row['option_d']
                    ];

                    foreach($options as $key => $val):
                        $row_class = "";
                        // If student picked this option
                        if($key == $row['selected_option']) {
                            $row_class = $row['is_correct'] ? 'opt-is-correct' : 'opt-selected-wrong';
                        } 
                        // If this is the correct answer but the student missed it
                        elseif($key == $row['correct_option']) {
                            $row_class = 'opt-is-correct';
                        }
                ?>
                    <div class="option-row <?php echo $row_class; ?>">
                        <span><strong><?php echo $key; ?>.</strong> <?php echo htmlspecialchars($val); ?></span>
                        
                        <div>
                            <?php if($key == $row['selected_option']): ?>
                                <span class="label-pill pill-user">Student's Pick</span>
                            <?php endif; ?>
                            <?php if($key == $row['correct_option']): ?>
                                <span class="label-pill pill-correct">Correct Answer</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div style="text-align: center; padding: 40px; color: #888;">
            <i class="fas fa-exclamation-triangle" style="font-size: 40px; margin-bottom: 10px;"></i>
            <p>No detailed response data found for this attempt.<br><small>(Detailed tracking only works for quizzes taken after the update)</small></p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
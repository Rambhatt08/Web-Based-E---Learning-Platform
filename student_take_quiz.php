<?php
session_start();
require 'db_connect.php';

// SECURITY
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

if (!isset($_GET['quiz_id'])) {
    die("No quiz selected!");
}
$quiz_id = intval($_GET['quiz_id']);

// Fetch Quiz details
$quiz = $conn->query("SELECT * FROM quizzes WHERE id = $quiz_id")->fetch_assoc();

// Fetch Questions
$questions_res = $conn->query("SELECT * FROM quiz_questions WHERE quiz_id = $quiz_id");
$questions = [];
while($q = $questions_res->fetch_assoc()){
    $questions[] = $q;
}

$total_questions = count($questions);
$is_submitted = false;
$score = 0;
$user_answers = []; // Array to track what the user picked

// GRADING THE QUIZ
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_quiz'])) {
    $is_submitted = true;
    
    // --- STEP A: CAPTURE ANSWERS FIRST ---
    foreach ($questions as $q) {
        $q_id = $q['id'];
        
        // Check if student selected an answer for this question
        if (isset($_POST['answer_'.$q_id])) {
            $student_answer = $_POST['answer_'.$q_id];
            $user_answers[$q_id] = $student_answer; // Store 'A', 'B', 'C', or 'D'
            
            // Check if correct
            if ($student_answer === $q['correct_option']) {
                $score++;
            }
        } else {
            $user_answers[$q_id] = null; // They left it blank
        }
    }

    // --- STEP B: SAVE MAIN ATTEMPT ---
    $stmt = $conn->prepare("INSERT INTO quiz_attempts (quiz_id, user_id, score, total_questions) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiii", $quiz_id, $user_id, $score, $total_questions);
    $stmt->execute();
    
    // Get the ID of the attempt we just saved
    $attempt_id = $conn->insert_id;

    // --- STEP C: SAVE INDIVIDUAL RESPONSES (THE FIX) ---
    foreach ($questions as $q) {
        $q_id = $q['id'];
        $selected = $user_answers[$q_id]; // Use the captured answer
        $is_correct = ($selected === $q['correct_option']) ? 1 : 0;

        // Ensure we save the letter properly into the database
        $res_stmt = $conn->prepare("INSERT INTO quiz_responses (attempt_id, question_id, selected_option, is_correct) VALUES (?, ?, ?, ?)");
        $res_stmt->bind_param("iisi", $attempt_id, $q_id, $selected, $is_correct);
        $res_stmt->execute();
    }
}

// --- DYNAMIC GREETING LOGIC ---
$greeting_msg = "";
$greeting_color = "";

if ($is_submitted && $total_questions > 0) {
    $percentage = ($score / $total_questions) * 100;

    if ($percentage == 100) {
        $greeting_msg = "Perfect Score! Outstanding! 🏆";
        $greeting_color = "#2ecc71"; // Green
    } elseif ($percentage >= 80) {
        $greeting_msg = "Excellent work! Keep it up. 🌟";
        $greeting_color = "#27ae60"; // Darker Green
    } elseif ($percentage >= 60) {
        $greeting_msg = "Good job! You did well. 👍";
        $greeting_color = "#3b82f6"; // Blue
    } elseif ($percentage >= 40) {
        $greeting_msg = "Not bad, but room for improvement. 📚";
        $greeting_color = "#f39c12"; // Orange
    } else {
        $greeting_msg = "Better luck next time! Keep learning. 💪";
        $greeting_color = "#e74c3c"; // Red
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Take Quiz - Smart Learn</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #f0f2f5; font-family: 'Poppins', sans-serif; display:flex; flex-direction:column; min-height:100vh;}
        .navbar { background: white; padding: 15px 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items:center;}
        
        .quiz-container { max-width: 800px; margin: 40px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); flex:1; width:100%;}
        .quiz-header { border-bottom: 2px solid #f0f0f0; padding-bottom: 15px; margin-bottom: 25px; text-align: center; }
        .quiz-header h2 { margin: 0; color: #2c3e50; }
        
        .question-card { margin-bottom: 30px; }
        .question-text { font-size: 18px; font-weight: 500; margin-bottom: 15px; color: #333; }
        
        .option-label { display: block; padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; margin-bottom: 10px; cursor: pointer; transition: 0.2s; background: #fafafa;}
        .option-label:hover { background: #f0f0f0; border-color:#ccc; }
        .option-label input { margin-right: 10px; }

        .btn-submit { background: #3b82f6; color: white; border: none; padding: 12px 30px; border-radius: 6px; font-size: 16px; cursor: pointer; width: 100%; font-weight: 600; margin-top:20px; text-decoration:none; display:inline-block; text-align:center;}
        .btn-submit:hover { background: #2563eb; }

        .results-box { text-align: center; padding: 20px 20px 40px; border-bottom: 2px dashed #eee; margin-bottom: 30px; }
        .score-circle { width: 120px; height: 120px; border-radius: 50%; background: #e3f2fd; color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 35px; font-weight: bold; margin: 0 auto 20px; border: 5px solid #3b82f6;}
        .greeting-text { font-size: 24px; font-weight: 600; margin-bottom: 15px; }
        
        .option-review { padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; margin-bottom: 10px; background: #fafafa; display: flex; justify-content: space-between; align-items: center;}
        .option-review.correct { background: #d4edda; border-color: #c3e6cb; color: #155724; font-weight: 500; }
        .option-review.wrong { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .review-icon { font-size: 18px; }
    </style>
</head>
<body>

    <div class="navbar">
        <h2>Smart Learn | Quiz Module</h2>
        <a href="quiz_attempts.php" style="text-decoration:none; color:#3b82f6; font-weight:500;">&larr; Back to Quiz Center</a>
    </div>

    <div class="quiz-container">
        <div class="quiz-header">
            <h2><?php echo htmlspecialchars($quiz['title']); ?></h2>
            <p style="color:#777;">Total Questions: <?php echo $total_questions; ?></p>
        </div>

        <?php if ($total_questions == 0): ?>
            <div style="text-align:center; padding: 40px; color:#888;">No questions have been added to this quiz yet.</div>
        
        <?php elseif ($is_submitted): ?>
            <div class="results-box">
                <div class="greeting-text" style="color: <?php echo $greeting_color; ?>;">
                    <?php echo $greeting_msg; ?>
                </div>
                <p>Here is your final score:</p>
                <div class="score-circle" style="color: <?php echo $greeting_color; ?>; border-color: <?php echo $greeting_color; ?>; background: white;">
                    <?php echo $score; ?>/<?php echo $total_questions; ?>
                </div>
                <a href="quiz_attempts.php" class="btn-submit" style="width:auto;">Return to Quiz Center</a>
            </div>

            <h3 style="margin-bottom: 20px; color:#333;">Review Your Answers:</h3>
            
            <?php 
            $q_num = 1; 
            foreach ($questions as $q): 
                $q_id = $q['id'];
                $correct_ans = $q['correct_option'];
                $student_ans = isset($user_answers[$q_id]) ? $user_answers[$q_id] : null;
                
                $options = [
                    'A' => $q['option_a'],
                    'B' => $q['option_b'],
                    'C' => $q['option_c'],
                    'D' => $q['option_d']
                ];
            ?>
                <div class="question-card">
                    <div class="question-text"><?php echo $q_num . ". " . htmlspecialchars($q['question_text']); ?></div>
                    
                    <?php foreach($options as $letter => $text): 
                        $class = "option-review";
                        $icon = "";
                        
                        if ($letter === $correct_ans) {
                            $class .= " correct"; 
                            $icon = '<i class="fas fa-check-circle review-icon"></i>';
                        } elseif ($letter === $student_ans && $student_ans !== $correct_ans) {
                            $class .= " wrong"; 
                            $icon = '<i class="fas fa-times-circle review-icon"></i>';
                        }
                    ?>
                        <div class="<?php echo $class; ?>">
                            <div><strong><?php echo $letter; ?>.</strong> <?php echo htmlspecialchars($text); ?></div>
                            <?php echo $icon; ?>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if(!$student_ans): ?>
                        <div style="color:#e74c3c; font-size:13px; margin-top:5px; font-style:italic;">You did not answer this question.</div>
                    <?php endif; ?>
                </div>
            <?php $q_num++; endforeach; ?>

        <?php else: ?>
            <form method="POST" action="">
                <input type="hidden" name="submit_quiz" value="1">
                
                <?php $q_num = 1; foreach ($questions as $q): ?>
                    <div class="question-card">
                        <div class="question-text"><?php echo $q_num . ". " . htmlspecialchars($q['question_text']); ?></div>
                        
                        <label class="option-label">
                            <input type="radio" name="answer_<?php echo $q['id']; ?>" value="A" required> 
                            <?php echo htmlspecialchars($q['option_a']); ?>
                        </label>
                        
                        <label class="option-label">
                            <input type="radio" name="answer_<?php echo $q['id']; ?>" value="B"> 
                            <?php echo htmlspecialchars($q['option_b']); ?>
                        </label>
                        
                        <label class="option-label">
                            <input type="radio" name="answer_<?php echo $q['id']; ?>" value="C"> 
                            <?php echo htmlspecialchars($q['option_c']); ?>
                        </label>
                        
                        <label class="option-label">
                            <input type="radio" name="answer_<?php echo $q['id']; ?>" value="D"> 
                            <?php echo htmlspecialchars($q['option_d']); ?>
                        </label>
                    </div>
                <?php $q_num++; endforeach; ?>

                <button type="submit" class="btn-submit" onclick="return confirm('Are you sure you want to submit your answers?')">Submit Quiz</button>
            </form>
        <?php endif; ?>

    </div>
</body>
</html>
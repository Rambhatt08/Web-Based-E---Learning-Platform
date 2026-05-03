<?php
session_start();
require 'db_connect.php';

// SECURITY CHECK
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";

// --- ACTIONS ---

// 1. Add New Quiz
if (isset($_POST['add_quiz'])) {
    $course_id = intval($_POST['course_id']);
    $title = $conn->real_escape_string($_POST['title']);
    $conn->query("INSERT INTO quizzes (course_id, title) VALUES ($course_id, '$title')");
    $message = "Quiz created successfully!";
}

// 2. Delete Quiz
if (isset($_GET['delete_quiz'])) {
    $id = intval($_GET['delete_quiz']);
    $conn->query("DELETE FROM quizzes WHERE id = $id");
    header("Location: admin_manage_quizzes.php");
    exit();
}

// 3. Add Question to Quiz
if (isset($_POST['add_question'])) {
    $quiz_id = intval($_POST['quiz_id']);
    $q_text = $conn->real_escape_string($_POST['question_text']);
    $opt_a = $conn->real_escape_string($_POST['option_a']);
    $opt_b = $conn->real_escape_string($_POST['option_b']);
    $opt_c = $conn->real_escape_string($_POST['option_c']);
    $opt_d = $conn->real_escape_string($_POST['option_d']);
    $correct = $conn->real_escape_string($_POST['correct_option']);

    $sql = "INSERT INTO quiz_questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option) 
            VALUES ($quiz_id, '$q_text', '$opt_a', '$opt_b', '$opt_c', '$opt_d', '$correct')";
    $conn->query($sql);
    $message = "Question added!";
}

// 4. Delete Question
if (isset($_GET['delete_question']) && isset($_GET['quiz_id'])) {
    $q_id = intval($_GET['delete_question']);
    $quiz_id = intval($_GET['quiz_id']);
    $conn->query("DELETE FROM quiz_questions WHERE id = $q_id");
    header("Location: admin_manage_quizzes.php?manage_id=" . $quiz_id);
    exit();
}

// --- FETCH DATA ---
$courses = $conn->query("SELECT id, title FROM courses ORDER BY id DESC");
$quizzes = $conn->query("SELECT q.*, c.title AS course_title FROM quizzes q JOIN courses c ON q.course_id = c.id ORDER BY q.created_at DESC");

// Are we viewing the "Manage Questions" screen?
$manage_id = isset($_GET['manage_id']) ? intval($_GET['manage_id']) : 0;
if ($manage_id > 0) {
    $active_quiz = $conn->query("SELECT * FROM quizzes WHERE id = $manage_id")->fetch_assoc();
    $questions = $conn->query("SELECT * FROM quiz_questions WHERE quiz_id = $manage_id");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Quizzes - Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { display: flex; background: #f4f7f6; margin: 0; min-height: 100vh; font-family: 'Poppins', sans-serif; }
        .sidebar { width: 260px; background: #2c3e50; color: white; display: flex; flex-direction: column; position: fixed; height: 100%; }
        .sidebar h2 { text-align: center; padding: 20px 0; background: #1a252f; margin: 0; }
        .sidebar a { padding: 15px 20px; color: #b8c7ce; text-decoration: none; border-left: 3px solid transparent; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: #34495e; color: white; border-left-color: #3498db; }
        .sidebar i { margin-right: 10px; width: 20px; }
        .main-content { flex: 1; margin-left: 260px; padding: 30px; }

        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Poppins', sans-serif; }
        .btn { background: #3b82f6; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; transition: 0.2s; text-decoration: none; display: inline-block;}
        .btn:hover { background: #2563eb; }
        .btn-danger { background: #e74c3c; } .btn-danger:hover { background: #c0392b; }
        .btn-success { background: #2ecc71; } .btn-success:hover { background: #27ae60; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f8f9fa; }
        .alert { padding: 10px; background: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Smart Learn</h2>
        <a href="admin-dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="admin_manage_courses.php"><i class="fas fa-video"></i> Manage Courses</a>
        <a href="admin_manage_notes.php"><i class="fas fa-file-pdf"></i> Manage Notes</a>
        <a href="admin_manage_ebooks.php"><i class="fas fa-book"></i> Manage E-Books</a>
        <a href="admin_students.php"><i class="fas fa-users"></i> Registered Students</a>
        <a href="admin_enrollments.php"><i class="fas fa-graduation-cap"></i> Enrolled History</a>
        <a href="admin_reviews.php"><i class="fas fa-star"></i> Manage Reviews</a>
        <a href="admin_queries.php"><i class="fas fa-envelope"></i> Queries & Subs</a>
        <a href="admin_manage_quizzes.php"><i class="fas fa-question-circle"></i> Manage Quizzes</a>
        <a href="admin_quiz_results.php"><i class="fas fa-chart-line"></i> Quiz Results</a>
        <a href="admin_manage_qa.php"><i class="fas fa-comments"></i> Student Q&A</a>

        <a href="logout.php" style="margin-top: auto; background: #c0392b;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>


    <div class="main-content">
        <?php if ($message): ?> <div class="alert"><?php echo $message; ?></div> <?php endif; ?>

        <?php if ($manage_id == 0): ?>
            <div class="card">
                <h3>Create New Quiz</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Select Course</label>
                        <select name="course_id" class="form-control" required>
                            <option value="">-- Choose Course --</option>
                            <?php while($c = $courses->fetch_assoc()): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['title']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quiz Title (e.g., 'Chapter 1 Assessment')</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <button type="submit" name="add_quiz" class="btn">Create Quiz</button>
                </form>
            </div>

            <div class="card">
                <h3>Existing Quizzes</h3>
                <table>
                    <tr><th>Quiz Title</th><th>Course</th><th>Date Created</th><th>Actions</th></tr>
                    <?php while($q = $quizzes->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($q['title']); ?></td>
                        <td><?php echo htmlspecialchars($q['course_title']); ?></td>
                        <td><?php echo date("M d, Y", strtotime($q['created_at'])); ?></td>
                        <td>
                            <a href="admin_manage_quizzes.php?manage_id=<?php echo $q['id']; ?>" class="btn btn-success" style="padding: 5px 10px; font-size:12px;">Add/Edit Questions</a>
                            <a href="admin_manage_quizzes.php?delete_quiz=<?php echo $q['id']; ?>" class="btn btn-danger" style="padding: 5px 10px; font-size:12px;" onclick="return confirm('Delete this quiz entirely?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>

        <?php else: ?>
            <div style="margin-bottom: 20px;">
                <a href="admin_manage_quizzes.php" class="btn" style="background:#7f8c8d;">&larr; Back to Quizzes</a>
            </div>

            <div class="card">
                <h3>Add Question to: <?php echo htmlspecialchars($active_quiz['title']); ?></h3>
                <form method="POST">
                    <input type="hidden" name="quiz_id" value="<?php echo $manage_id; ?>">
                    <div class="form-group">
                        <label>Question Text</label>
                        <input type="text" name="question_text" class="form-control" required>
                    </div>
                    <div style="display:flex; gap:15px;">
                        <div class="form-group" style="flex:1;"><label>Option A</label><input type="text" name="option_a" class="form-control" required></div>
                        <div class="form-group" style="flex:1;"><label>Option B</label><input type="text" name="option_b" class="form-control" required></div>
                    </div>
                    <div style="display:flex; gap:15px;">
                        <div class="form-group" style="flex:1;"><label>Option C</label><input type="text" name="option_c" class="form-control" required></div>
                        <div class="form-group" style="flex:1;"><label>Option D</label><input type="text" name="option_d" class="form-control" required></div>
                    </div>
                    <div class="form-group">
                        <label>Correct Answer</label>
                        <select name="correct_option" class="form-control" required>
                            <option value="A">Option A</option>
                            <option value="B">Option B</option>
                            <option value="C">Option C</option>
                            <option value="D">Option D</option>
                        </select>
                    </div>
                    <button type="submit" name="add_question" class="btn">Add Question</button>
                </form>
            </div>

            <div class="card">
                <h3>Questions in this Quiz</h3>
                <table>
                    <tr><th>Question</th><th>Correct Answer</th><th>Action</th></tr>
                    <?php while($question = $questions->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($question['question_text']); ?></td>
                        <td><strong><?php echo $question['correct_option']; ?></strong></td>
                        <td><a href="admin_manage_quizzes.php?manage_id=<?php echo $manage_id; ?>&delete_question=<?php echo $question['id']; ?>&quiz_id=<?php echo $manage_id; ?>" class="btn btn-danger" style="padding: 5px 10px; font-size:12px;">Remove</a></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>
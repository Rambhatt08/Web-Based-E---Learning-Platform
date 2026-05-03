<?php
session_start();
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Get data from form
    $user_input = $_POST['username_email'];
    $password = $_POST['password'];
    $captcha_user_answer = $_POST['captcha'];

    // 2. CHECK DYNAMIC CAPTCHA
    // Compare what user typed vs. the correct answer stored in Session
    if (!isset($_SESSION['captcha_correct']) || $captcha_user_answer != $_SESSION['captcha_correct']) {
        echo "<script>alert('Incorrect Math Answer! Please try again.'); window.location.href='login.php';</script>";
        exit();
    }

    // 3. UPDATED QUERY: We select 'id', 'User_Name', 'password', AND 'role' now
    $stmt = $conn->prepare("SELECT id, User_Name, password, role FROM users WHERE email = ? OR User_Name = ?");
    $stmt->bind_param("ss", $user_input, $user_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        
        if (password_verify($password, $row['password'])) {
            
            // 4. GET DATA for Session
            $user_id = $row['id'];
            $user_name = $row['User_Name'];
            $role = $row['role']; // <--- NEW: Get the role (admin/student)

            // Set Session Variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $user_name;
            $_SESSION['role'] = $role; // <--- NEW: Save role to session

            // 5. INSERT INTO LOGS (With Name)
            $log_stmt = $conn->prepare("INSERT INTO login_logs (user_id, user_name) VALUES (?, ?)");
            $log_stmt->bind_param("is", $user_id, $user_name);
            $log_stmt->execute();
            $log_stmt->close();

            // 6. REDIRECT BASED ON ROLE
            if ($role === 'admin') {
                header("Location: admin-dashboard.php"); // Redirect Admins here
            } else {
                header("Location: student-dashboard.php"); // Redirect Students here
            }
            exit();

        } else {
            echo "<script>alert('Invalid Password!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('User not found.'); window.location.href='register.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
<?php
require 'db_connect.php';
// 1. Keep the Timezone Fix (Critical!)
date_default_timezone_set('Asia/Kolkata'); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_input = $_POST['email'];

    // 2. Check if user exists
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ? OR User_Name = ?");
    $stmt->bind_param("ss", $email_input, $email_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $real_email = $user['email'];

        // 3. Generate Token
        $token = bin2hex(random_bytes(32)); 
        $expires = date("Y-m-d H:i:s", time() + 3600); // 1 hour from now

        // 4. Save to Database (Using the method that we know works)
        $insertStmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $insertStmt->bind_param("sss", $real_email, $token, $expires);

        if ($insertStmt->execute()) {
            // SUCCESS: Show the Popup Link
            $link = "http://localhost/smartlearn/reset_password.php?token=" . $token;
            
            echo "<script>
                    window.prompt('DEMO MODE: Copy this link and pest it in the browser to reset password:', '$link');
                    window.location.href = 'login.php'; 
                  </script>";
        } else {
            // If it fails silently, alert the user
            echo "<script>alert('System Error: Could not generate link. Please try again.'); window.history.back();</script>";
        }
        
        $insertStmt->close();
    } else {
        echo "<script>alert('No account found with that email/username.'); window.history.back();</script>";
    }
    
    $stmt->close();
    $conn->close();
}
?>
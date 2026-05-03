<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_pass = $_POST['password'];

    // 1. Find the email associated with this token
    $result = $conn->query("SELECT email FROM password_resets WHERE token='$token' LIMIT 1");

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['email'];

        // 2. Hash the new password
        $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);

        // 3. Update the USERS table
        $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update->bind_param("ss", $hashed_password, $email);
        
        if ($update->execute()) {
            // 4. Delete the token so it can't be used again
            $conn->query("DELETE FROM password_resets WHERE email='$email'");
            
            echo "<script>alert('Password Updated Successfully! Please Login.'); window.location.href='login.php';</script>";
        }
    } else {
        echo "Invalid Request.";
    }
}
?>
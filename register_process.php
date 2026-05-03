<?php
// 1. Connect to the Database
require 'db_connect.php';

// 2. Check if the form was submitted    
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Get data from the form
    $First_Name = $_POST['First_Name'];
    $Last_Name  = $_POST['Last_Name']; 
    $User_Name  = $_POST['User_Name'];
    $email      = $_POST['email'];
    $password   = $_POST['password'];
    $confirm_password = $_POST['Password_confirmation'];

    // --- NEW LOGIC START: Check for Admin Key ---
    // Define the secret password for teachers
    $SECRET_KEY = "Teacher2026"; 
    $user_role = 'student'; // Default role is always student

    // Check if the user typed anything in the secret box
    if (isset($_POST['admin_key']) && $_POST['admin_key'] === $SECRET_KEY) {
        $user_role = 'admin'; // Upgrade to Admin!
    }
    // --- NEW LOGIC END ---

    // 4. VALIDATION: Check if passwords match
    if ($password !== $confirm_password) {
        die("Error: Passwords do not match.");
    }

    // 5. Check if email or username already exists
    // (Using prepared statement for better security)
    $checkUser = $conn->prepare("SELECT email FROM users WHERE email = ? OR User_Name = ?");
    $checkUser->bind_param("ss", $email, $User_Name);
    $checkUser->execute();
    $result = $checkUser->get_result();
    
    if ($result->num_rows > 0) {
        die("Error: Email or Username is already taken.");
    }

    // 6. Secure the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 7. Insert into Database (Updated to include 'role')
    // We use prepared statements here to prevent SQL Injection
    $stmt = $conn->prepare("INSERT INTO users (First_Name, Last_Name, User_Name, email, password, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $First_Name, $Last_Name, $User_Name, $email, $hashed_password, $user_role);

    if ($stmt->execute()) {
        // Show success message and redirect
        echo "<script>
                alert('Registration Successful! You are registered as: $user_role');
                window.location.href='login.php';
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
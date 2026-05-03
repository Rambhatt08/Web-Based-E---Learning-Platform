<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];

    // 1. Check if email already exists
    $checkSql = "SELECT email FROM newsletter WHERE email = '$email'";
    $result = $conn->query($checkSql);

    if ($result->num_rows > 0) {
        // Already subscribed
        echo "<script>alert('You are already subscribed!'); window.history.back();</script>";
    } else {
        // 2. Insert new email
        // Using Prepared Statements for security
        $stmt = $conn->prepare("INSERT INTO newsletter (email) VALUES (?)");
        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            echo "<script>alert('Subscription Successful! Thank you.'); window.history.back();</script>";
        } else {
            echo "Error: " . $conn->error;
        }
        $stmt->close();
    }
    
    $conn->close();
}
?>
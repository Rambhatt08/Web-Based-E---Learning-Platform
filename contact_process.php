<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Get data from form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // 2. Prepare SQL Statement (Prevents SQL Injection)
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    // 3. Execute and Redirect
    if ($stmt->execute()) {
        // Success! Show alert and go back to contact page
        echo "<script>
                alert('Message Sent Successfully! We will contact you soon.'); 
                window.location.href='contact.php';
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Smart Learn</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="imgs/favicon.png" type="image/png">

</head>
<body>

    <nav class="navbar">
        <div class="logo">
            <a href="index.html"><img src="imgs\logo.jpg" alt="Smart Learn Logo"></a>
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="about.html">About US</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="#" class="active">Dashboard</a></li>
        </ul>
    </nav>

    <section class="forgot-password-section">
        <div class="reset-container">
            <p class="reset-text">
                Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.
            </p>

            <form action="send_reset_link.php" method="POST" class="reset-form">
                <label for="user-email">Username or email</label>
                <input type="text" name="email" placeholder="Enter your email" id="user-email" class="input-field" required>
                
                <button type="submit" class="btn-reset">Reset password</button>
            </form>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-col">
            <img src="imgs\1.png" alt="Smart Learn" class="footer-logo">
            <p class="slogan">Learning often happens in<br> classrooms <br> but it doesn't have to.</p>
        </div>
        <div class="footer-col">
            <h4>Popular Contain</h4>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="#">Course</a></li>
                <li><a href="#">Dashboard</a></li>
                <li><a href="contact.php">Contact Us</a></li>
                <li><a href="about.html">About Us</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Contact Info</h4>
            <p>0123456789</p>
            <p>helpsmartlearn@gmail.com</p>
        </div>
    </footer>

</body>
</html>
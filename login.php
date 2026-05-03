<?php 
session_start(); 

// GENERATE CAPTCHA
// 1. Pick two random numbers between 1 and 20
$num1 = rand(1, 20);
$num2 = rand(1, 20);

// 2. Calculate the correct answer
$correct_answer = $num1 + $num2;

// 3. Save the answer in the Session so the backend can check it later
$_SESSION['captcha_correct'] = $correct_answer;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Login - Smart Learn</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="imgs/favicon.png" type="image/png">
</head>
<body>

    <nav class="navbar">
        <div class="logo">
            <a href="index.html"><img src="imgs\logo.jpg" alt="Smart learn Logo"></a>
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="about.html">About US</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="#" class="active">Dashboard</a></li>
        </ul>
    </nav>

    <section class="login-container">
        <div class="login-card">
            <h2>Hi, Welcome back!</h2>
            
            <form action="login_process.php" method="POST" class="login-form">
                <input type="text" name="username_email" placeholder="Username or Email Address" class="input-field" required>
               
                
                <div class="password-container" style="position: relative;">
                    <input type="password" name="password" id="passwordInput" placeholder="Password" class="input-field" required style="width: 100%; padding-right: 40px;">
    
                    <i class="fas fa-eye" id="togglePassword" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #333;"></i>
                </div>

                <div class="humanity-check">
                    <label>Prove your humanity: <?php echo $num1; ?> + <?php echo $num2; ?> = </label>
                    <input type="number" name="captcha" style="width: 60px;" required>
                </div>

                <div class="form-options">
                    <label class="checkbox-container">
                        <input type="checkbox"> Keep me signed in
                    </label>
                    <a href="forgot-password.php" class="forgot-pass">Forgot Password?</a>
                </div>

                <button type="submit" class="btn-signin">Sign In</button>
            </form>

            <p class="register-link">Don't have an account? <a href="register.php">Register Now</a></p>
        </div>
    </section>

    <a href="#" class="whatsapp-float">
        <i class="fab fa-whatsapp"></i>
    </a>

    <footer class="footer">
        <div class="footer-col">
            <img src="imgs\1.png" alt="Smart Learn" class="footer-logo">
            <p class="slogan">Learning often happens in<br> classrooms <br> but it doesn't have to.</p>
        </div>
        <div class="footer-col">
            <h4>Popular Contain</h4>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="#">Course</a></li> <!-- later change this this is only temporary access at date 12/03/26 i changed is to as "#"-->
                <li><a href="#">Dashboard</a></li>
                <li><a href="contact.php">Contact Us</a></li>
                <li><a href="about.html">About Us</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Contact Info</h4>
            <p>1234567890</p>
            <p>helpsmartlearn@gmail.com</p>
        </div>
    </footer>
<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#passwordInput');

    togglePassword.addEventListener('click', function (e) {
        // 1. Toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);

        // 2. Toggle the eye / eye-slash icon
        this.classList.toggle('fa-eye-slash');
    });
</script>
</body>
</html>
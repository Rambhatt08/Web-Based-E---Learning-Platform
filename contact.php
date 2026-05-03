<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Smart Learn </title>
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
            <li><a href="contact.php" class="active">Contact Us</a></li>
            <li><a href="#">Dashboard</a></li>
        </ul>
    </nav>

    <header class="page-header">
        <div class="header-content">
            <h1>Contact</h1>
            <p>Make learning and teaching more effective with active participation and student collaboration</p>
        </div>
    </header>

    <section class="contact-section">
        <h2 class="section-title">We are always open 24/7 <br> for you.</h2>
        
        <div class="contact-container">
            <form action="contact_process.php" method="POST" class="contact-form">
                <div class="form-group">
                    <label>Your name</label>
                    <input type="text" name="name" placeholder="" required>
                </div>
                
                <div class="form-group">
                    <label>Your email</label>
                    <input type="email" name="email" placeholder="" required>
                </div>
                
                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" name="subject" placeholder="">
                </div>
                
                <div class="form-group">
                    <label>Your message (optional)</label>
                    <textarea rows="6" name="message" placeholder=""></textarea>
                </div>
                
                <button type="submit" class="btn-submit">Submit</button>
            </form>

            <div class="contact-info-box">
                <div class="info-item">
                    <h3>Call us</h3>
                    <p>+91-0123456789</p>
                </div>
                
                <div class="info-item">
                    <h3>Email us</h3>
                    <p>smartlearnhelp@gmail.com</p>
                </div>
            </div>
        </div>
    </section>

    <section class="stats-section">
        <div class="stats-container" style="margin-top: 0;">
            <div class="stat-box">
                <img src="imgs\icon-learners.png" alt="Icon">
                <p>Learners</p>
            </div>
            <div class="stat-box">
                <img src="imgs\icon-review.png" alt="Icon">
                <p>Review</p>
            </div>
            <div class="stat-box">
                <img src="imgs\icon-published.png" alt="Icon">
                <p>Course Published</p>
            </div>
        </div>
    </section>

    <section class="newsletter">
        <div class="newsletter-bg">
            <img src="imgs\graduates-bg.png" class="bg-img">
            <div class="newsletter-content">
                <h2>Want to get special offers <br> and Course updates?</h2>
                <form class="subscribe-form" action="subscribe_process.php" method="POST">
                    <input type="email" name="email" placeholder="Your email address" required>
                    <button type="submit">Subscribe</button>
                </form>
            </div>
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
        <p class="copyright">
        © <span id="year"></span> Smart Learn. All rights reserved.
        </p>
        <script src="script.js"></script>
    </footer>

</body>
</html>
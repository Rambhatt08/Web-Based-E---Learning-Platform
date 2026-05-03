<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Learn - E-Learning Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="imgs/favicon.png" type="image/png">
    

</head>
<body>

    <nav class="navbar">
        <div class="logo">
            <img src="imgs/logo.jpg" alt="Smart Learn Logo"> 
        </div>
        <div class="menu-toggle" onclick="toggleMenu()">
            <i class="fas fa-bars"></i>
        </div>
        <ul class="nav-links">
            <li><a href="index.php" class="active">Home</a></li>
            <li><a href="about.html">About US</a></li>
            <li><a href="contact.php">Contact Us</a></li>

            <?php if(isset($_SESSION['user_name'])): ?>
                <li><span style="color: #6c63ff; font-weight: bold;">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span></li>
                <li><a href="logout.php" style="color: red;">Logout</a></li>
                
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif; ?>
            </ul>
    </nav>

    <header class="hero">
        <div class="hero-content">
            <span class="subtitle">HELLO</span>
            <h1>Learning <br> keeps you in <br> the lead</h1>
            <a href="#courses" class="btn-outline">Browse Courses</a>
        </div>
        <div class="hero-image-container">
            <div class="purple-bg"></div>
            <img src="imgs\temp.jpg" alt="Student" class="main-hero-img">
        </div>
    </header>

    <section class="categories">
        <div class="category-card">
            <img src="imgs\icon-dbms.png.png" alt="DBMS">
            <h3>DBMS</h3>
        </div>
        <div class="category-card">
            <img src="imgs\icon-java.png.png" alt="Java">
            <h3>Java Programming</h3>
        </div>
        <div class="category-card">
            <img src="imgs\icon-soa.png.png" alt="SOA">
            <h3>SOA</h3>
        </div>
        <div class="category-card">
            <img src="imgs\icon-os.png.png" alt="OS">
            <h3>Operating System</h3>
        </div>
    </section>

    <section class="featured" id="courses">
        <div class="section-header">
            <h2>Featured Courses</h2>
            <button class="filter-btn">⚡ Software Engineering</button>
        </div>
        
        <div class="course-grid">
            <div class="course-card">
                <div class="course-img-wrapper">
                    <img src="imgs\course-java.png.png" alt="Java">
                </div>
                <div class="course-info">
                    <div class="stars">★★★★★</div>
                    <h3>Advance Java Programming</h3>
                    <button class="btn-course">Enroll Course</button>
                </div>
            </div>

            <div class="course-card">
                <div class="course-img-wrapper">
                    <img src="imgs\course-soa.png.png" alt="SOA">
                </div>
                <div class="course-info">
                    <div class="rating">★★★★★</div>
                    <h3>Service Oriented Architecture</h3>
                    <button class="btn-course">Enroll Course</button>
                </div>
            </div>

            <div class="course-card">
                <div class="course-img-wrapper">
                    <img src="imgs\course-se.png" alt="Software Engineering">
                </div>
                <div class="course-info">
                    <div class="rating">★★★★★</div>
                    <h3>Software Engineering</h3>
                    <button class="btn-course">Enroll Course</button>
                </div>
            </div>
        </div>
        <div class = "discover-more"><a href="login.php">Discover more</a></div>
    </section>

    <section class="levels-section">
        <h2>Things that make us proud</h2>
        <p class="section-sub">Choose Your Learning Level</p>

        <div class="levels-grid">
            <div class="level-item">
                <img src="imgs\icon-beginner.png" class="level-icon">
                <div>
                    <h3>Beginner</h3>
                    <p>Start your engineering journey with our easy-to-understand beginner courses that cover fundamental concepts and basics.</p>
                </div>
            </div>
            <div class="level-item">
                <img src="imgs\icon-intermediate.png" class="level-icon">
                <div>
                    <h3>Intermediate</h3>
                    <p>Deepen your understanding of engineering concepts with our comprehensive intermediate courses.</p>
                </div>
            </div>
            <div class="level-item">
                <img src="imgs\icon-advanced.png" class="level-icon">
                <div>
                    <h3>Advanced</h3>
                    <p>Enhance your expertise with our advanced courses that delve into complex engineering concepts.</p>
                </div>
            </div>
            <div class="level-item">
                <img src="imgs\icon-mastery.png" class="level-icon">
                <div>
                    <h3>Mastery</h3>
                    <p>Achieve the highest level of proficiency with our mastery courses, designed for in-depth exploration.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="stats-section">
        <h2>Everything is a learning experience</h2>
        <p>Exploring the complexities of IT engineering and its evolving landscape</p>
        
        <div class="stats-container">
            <div class="stat-box">
                <img src="imgs\icon-learners.png" alt="Icon">
                <h3></h3>
                <p>Learners</p>
            </div>
            
            <div class="stat-box">
                <img src="imgs\icon-review.png" alt="Icon">
                <h3></h3>
                <p>Review</p>
            </div>
            <div class="stat-box">
                <img src="imgs\icon-published.png" alt="Icon">
                <h3></h3>
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
            <p>smartlearnhelp@gmail.com</p>

    <p class="copyright">
        © <span id="year"></span> Smart Learn. All rights reserved.
    </p>

        </div>
    </footer>
                
    <script src="script.js"></script>
</body>
</html>
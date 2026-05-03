<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Smart Learn</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            <li><a href="#">Dashboard</a></li>
        </ul>    
    </nav>

    <section class="register-section">
        <div class="register-card">
            
            <form action="register_process.php" method="POST" class="register-form">
                
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="First_Name" placeholder="First Name" class="input-field" required>
                </div>

                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="Last_Name" placeholder="Last Name" class="input-field" required>
                </div>

                <div class="form-group">
                    <label>User Name</label>
                    <input type="text" name="User_Name" placeholder="User Name" class="input-field" required>
                </div>

                <div class="form-group">
                    <label>E-Mail</label>
                    <input type="email" name="email"  placeholder="E-Mail" class="input-field" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div style="position: relative;">
                        <input type="password" name="password" placeholder="Password" class="input-field" style="width: 100%; padding-right: 40px;" required>
                        <i class="fas fa-eye toggle-password" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #333;"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password confirmation</label>
                    <div style="position: relative;">
                        <input type="password" name="Password_confirmation" placeholder="Password Confirmation" class="input-field" style="width: 100%; padding-right: 40px;" required>
                        <i class="fas fa-eye toggle-password" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #333;"></i>
                    </div>
                </div>



                <div class="form-options">
                    <label class="checkbox-container">
                        <input type="checkbox"> Sign me up for the newsletter!
                    </label>
                </div>
            <div class="form-group">
    <label>Admin Key (Optional)</label>
    
    <div style="position: relative;">
        <input type="password" 
               name="admin_key" 
               class="input-field" 
               placeholder="For Instructors Only" 
               autocomplete="off" 
               style="width: 100%; padding-right: 40px;">
        
        <i class="fas fa-eye toggle-password" 
           style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #333;">
        </i>
    </div>

    <small style="color: #333; display: block; margin-top: 5px;">Leave empty if you are a student.</small>
</div>


                <button type="submit" class="btn-signin">Register</button>
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
                <li><a href="index.html">Home</a></li>
                <li><a href="#">Course</a></li>
                <li><a href="dashboard.html">Dashboard</a></li>
                <li><a href="contact.html">Contact Us</a></li>
                <li><a href="about.html">About Us</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Contact Info</h4>
            <p>0123456789</p>
            <p>helpsmartlearn@gmail.com</p>
        </div>
    </footer>
<script>
    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', function () {
            // 1. Find the input field
            const input = this.previousElementSibling;
            
            // 2. Toggle type (password <-> text)
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            
            // 3. TOGGLE THE ICON (Swap the classes)
            // This removes 'fa-eye' and adds 'fa-eye-slash', or vice versa
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    });
</script>
</body>
</html>
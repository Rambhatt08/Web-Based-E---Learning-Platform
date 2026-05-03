<?php 
require 'db_connect.php';
date_default_timezone_set('Asia/Kolkata');

// 1. Verify Token
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $result = $conn->query("SELECT * FROM password_resets WHERE token='$token' AND expires_at > NOW()");
    
    if ($result->num_rows == 0) {
        // Token Invalid? Show a pretty error page instead of white screen
        die("
            <div style='text-align:center; padding: 50px; font-family: sans-serif;'>
                <h1 style='color: #dc3545;'>Link Expired</h1>
                <p>This password reset link is invalid or has expired.</p>
                <a href='forgot_password.php' style='color: #007bff;'>Request a new link</a>
            </div>
        ");
    }
} else {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Smart Learn</title>
    
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Extra styles to center the card perfectly */
        body {
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .reset-card {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .logo-img {
            width: 150px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
    </style>
</head>
<body>

    <div class="reset-card">
        <img src="imgs/1.png" alt="Smart Learn Logo" class="logo-img">
        
        <h2>Set New Password</h2>
        <p style="color: #666; margin-bottom: 30px;">Please create a strong password for your account.</p>

        <form action="update_password_final.php" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            
            <div class="form-group">
                <label>New Password</label>
                <div style="position: relative;">
                    <input type="password" name="password" class="input-field" placeholder="New Password" required style="width: 100%; padding: 12px; padding-right: 40px; border: 1px solid #ddd; border-radius: 5px;">
                    <i class="fas fa-eye toggle-password" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #333;"></i>
                </div>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <div style="position: relative;">
                    <input type="password" name="confirm_password" class="input-field" placeholder="Confirm New Password" required style="width: 100%; padding: 12px; padding-right: 40px; border: 1px solid #ddd; border-radius: 5px;">
                    <i class="fas fa-eye toggle-password" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #333;"></i>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; background-color: #2878EB; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
                Update Password
            </button>
        </form>
        
        <div style="margin-top: 20px;">
            <a href="login.php" style="text-decoration: none; color: #666; font-size: 14px;">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>
        </div>
    </div>

    <script>
        document.querySelectorAll('.toggle-password').forEach(icon => {
            icon.addEventListener('click', function () {
                const input = this.previousElementSibling;
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        });
    </script>

</body>
</html>
<?php
session_start();
require 'db_connect.php';

// SECURITY: Strict check for Admin Role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// --- HANDLE DELETION ---
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    
    // Optional: You could check if this user has enrollments and delete those first
    // For now, we just delete the user
    $sql = "DELETE FROM users WHERE id = $id";
    
    if ($conn->query($sql)) {
        header("Location: admin_students.php"); // Refresh page
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// --- FETCH STUDENTS ONLY ---
// We filter by role='student' so we don't accidentally list Admins here
$sql = "SELECT * FROM users WHERE role = 'student' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registered Students - Smart Learn</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; padding: 20px; }
        .container { max-width: 1100px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        h2 { margin: 0; color: #2c3e50; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #3498db; color: white; font-weight: 600; }
        tr:hover { background-color: #f1f1f1; }
        
        /* Profile Icon for visual appeal */
        .profile-icon { color: #bdc3c7; font-size: 24px; margin-right: 10px; vertical-align: middle; }
        
        /* Action Buttons */
        .btn-delete { color: #e74c3c; text-decoration: none; font-size: 18px; transition: 0.2s; }
        .btn-delete:hover { transform: scale(1.2); }
        
        .back-link { display: inline-block; margin-top: 20px; text-decoration: none; color: #555; font-weight: bold; }
        .back-link:hover { color: #3498db; }

        .empty-state { text-align: center; padding: 40px; color: #777; font-size: 18px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2><i class="fas fa-users"></i> Registered Students</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Joined Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    
                    // Combine First and Last Name
                    $fullName = htmlspecialchars($row['First_Name'] . " " . $row['Last_Name']);
                    echo "<td><i class='fas fa-user-circle profile-icon'></i> " . $fullName . "</td>";
                    
                    echo "<td>@" . htmlspecialchars($row['User_Name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    
                    // Format the date
                    $date = date("M d, Y", strtotime($row['created_at']));
                    echo "<td>" . $date . "</td>";
                    
                    echo "<td>
                            <a href='admin_students.php?delete_id=" . $row['id'] . "' 
                               class='btn-delete' 
                               onclick='return confirm(\"Are you sure? This will delete the student account permanently.\")' 
                               title='Delete Student'>
                               <i class='fas fa-trash-alt'></i>
                            </a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='empty-state'>No students registered yet.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <a href="admin-dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
</div>

</body>
</html>
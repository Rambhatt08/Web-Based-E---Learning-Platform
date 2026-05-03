<?php
session_start();
require 'db_connect.php';

// SECURITY: Strict check for Admin Role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// --- HANDLE DELETIONS ---

// 1. Delete a Message
if (isset($_GET['del_msg'])) {
    $id = intval($_GET['del_msg']);
    $conn->query("DELETE FROM contact_messages WHERE id=$id");
    header("Location: admin_queries.php"); // Refresh page
    exit();
}

// 2. Delete a Subscriber
if (isset($_GET['del_sub'])) {
    $id = intval($_GET['del_sub']);
    $conn->query("DELETE FROM newsletter WHERE id=$id");
    header("Location: admin_queries.php"); // Refresh page
    exit();
}

// --- FETCH DATA ---
$messages = $conn->query("SELECT * FROM contact_messages ORDER BY submitted_at DESC");
$subscribers = $conn->query("SELECT * FROM newsletter ORDER BY subscribed_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Queries & Subscribers - Smart Learn</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; padding: 20px; }
        .container { max-width: 1100px; margin: 0 auto; }
        
        .section { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 30px; }
        h2 { margin-top: 0; color: #2c3e50; border-bottom: 2px solid #f4f7f6; padding-bottom: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; font-size: 14px; }
        th { background-color: #f8f9fa; color: #333; font-weight: 600; }
        tr:hover { background-color: #f1f1f1; }
        
        /* Delete Button Styling */
        .btn-delete { color: #e74c3c; cursor: pointer; text-decoration: none; font-size: 16px; transition: 0.2s; }
        .btn-delete:hover { transform: scale(1.2); }

        .date { color: #888; font-size: 12px; }
        .back-link { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #555; font-weight: bold; font-size: 18px; }
        .back-link:hover { color: #3498db; }
    </style>
</head>
<body>

<div class="container">
    <a href="admin-dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

    <div class="section">
        <h2><i class="fas fa-envelope-open-text"></i> User Queries (Contact Form)</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($messages->num_rows > 0): ?>
                    <?php while($row = $messages->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['subject']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
                        <td class="date"><?php echo date("M d, Y", strtotime($row['submitted_at'])); ?></td>
                        <td>
                            <a href="admin_queries.php?del_msg=<?php echo $row['id']; ?>" 
                               class="btn-delete" 
                               onclick="return confirm('Delete this message permanently?');"
                               title="Delete Message">
                               <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" style="text-align:center; padding: 20px; color: #777;">No new messages found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2><i class="fas fa-paper-plane"></i> Newsletter Subscribers</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email Address</th>
                    <th>Subscribed Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($subscribers->num_rows > 0): ?>
                    <?php while($row = $subscribers->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($row['email']); ?></strong></td>
                        <td class="date"><?php echo date("M d, Y H:i", strtotime($row['subscribed_at'])); ?></td>
                        <td>
                            <a href="admin_queries.php?del_sub=<?php echo $row['id']; ?>" 
                               class="btn-delete" 
                               onclick="return confirm('Remove this email from subscribers list?');"
                               title="Delete Subscriber">
                               <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center; padding: 20px; color: #777;">No subscribers yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
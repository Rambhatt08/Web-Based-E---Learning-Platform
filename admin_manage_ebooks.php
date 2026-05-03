<?php
session_start();
require 'db_connect.php';

// 1. Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 2. Handle Deletion
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    
    // Get file info first so we can delete the actual files from the folder
    $query = $conn->query("SELECT file_path, cover_image FROM ebooks WHERE id = $id");
    $file_data = $query->fetch_assoc();
    
    // Delete from Database
    $sql = "DELETE FROM ebooks WHERE id = $id";
    if ($conn->query($sql)) {
        // Delete physical files
        if (!empty($file_data['file_path']) && file_exists($file_data['file_path'])) {
            unlink($file_data['file_path']);
        }
        if (!empty($file_data['cover_image']) && file_exists($file_data['cover_image'])) {
            unlink($file_data['cover_image']);
        }
        
        // Refresh the page
        header("Location: admin_manage_ebooks.php"); 
        exit();
    }
}

// 3. Fetch Data
$sql = "SELECT * FROM ebooks ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage E-Books</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; margin-bottom: 20px; border-bottom: 2px solid #f4f4f4; padding-bottom: 15px; }
        .btn-add { background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; }
        .thumb-img { width: 40px; height: 50px; object-fit: cover; border: 1px solid #ddd; }
        .btn-delete { color: #dc3545; font-size: 18px; margin-left: 10px; }
        .back-link { display: inline-block; margin-top: 20px; text-decoration: none; color: #777; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>Manage E-Books</h2>
        <a href="admin_add_ebook.php" class="btn-add">+ Add New E-Book</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cover</th>
                <th>Title</th>
                <th>Branch / Year</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td>
                            <?php $cover = !empty($row['cover_image']) ? $row['cover_image'] : 'imgs/default-book.png'; ?>
                            <img src="<?php echo $cover; ?>" class="thumb-img">
                        </td>
                        <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                        <td><?php echo $row['branch']; ?> (<?php echo $row['year_level']; ?>)</td>
                        <td>
                            <a href="<?php echo $row['file_path']; ?>" download><i class="fas fa-download" style="color:#3498db;"></i></a>
                            <a href="admin_manage_ebooks.php?delete_id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Delete this E-Book?');"><i class="fas fa-trash-alt"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center; padding:30px;">No E-Books uploaded.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="admin-dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
</div>
</body>
</html>
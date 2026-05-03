<?php
session_start();

// 1. Connection
$host = "localhost";
$username = "root";
$password = "";
$dbname = "smartlearn_db";
$conn = mysqli_connect($host, $username, $password, $dbname);

// 2. Handle Deletion (If admin clicks Delete)
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    
    // Optional: You could also delete the actual file from the folder here using unlink()
    
    $sql = "DELETE FROM notes WHERE id = $id";
    mysqli_query($conn, $sql);
    header("Location: admin_manage_notes.php"); // Refresh page
    exit();
}

// 3. Fetch All Notes
$sql = "SELECT * FROM notes ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Notes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        h2 { margin: 0; color: #333; }
        
        /* Green Add Button */
        .btn-add { background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .btn-add:hover { background: #218838; }

        /* Table Styling */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; color: #333; }
        tr:hover { background-color: #f1f1f1; }

        /* Thumbnail Image in Table */
        .thumb-img { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; }
        
        /* Action Buttons */
        .btn-view { color: #007bff; margin-right: 10px; text-decoration: none; }
        .btn-delete { color: #dc3545; text-decoration: none; }
        
        .back-link { display: inline-block; margin-top: 20px; text-decoration: none; color: #555; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>Manage Notes</h2>
        <a href="admin_add_notes.php" class="btn-add"><i class="fa fa-plus"></i> Add New Note</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Thumb</th>
                <th>Title</th>
                <th>Branch / Year</th>
                <th>Subject</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Loop through database results
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    
                    // Show Thumbnail
                    echo "<td><img src='" . $row['thumbnail'] . "' class='thumb-img'></td>";
                    
                    echo "<td><strong>" . $row['title'] . "</strong></td>";
                    echo "<td>" . $row['branch'] . " (" . $row['year_level'] . ")</td>";
                    echo "<td>" . $row['subject'] . "</td>";
                    
                    echo "<td>
                            <a href='" . $row['file_path'] . "' target='_blank' class='btn-view' title='View PDF'><i class='fa fa-eye'></i></a>
                            
                            <a href='admin_manage_notes.php?delete_id=" . $row['id'] . "' class='btn-delete' onclick='return confirm(\"Are you sure?\")' title='Delete'><i class='fa fa-trash'></i></a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center;'>No notes uploaded yet.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <a href="admin-dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
</div>

</body>
</html>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sams_db"; // Database name

// Database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sqlStud = "SELECT * FROM student WHERE StudentID = " . $_GET['StudentID'];
$resultStud = $conn->query($sqlStud);
$row = $resultStud ->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
        // Check if the delete button was clicked
        if (isset($_POST['delete'])) {
            $stmt = $conn->prepare("DELETE FROM student WHERE StudentID = ?");
            $stmt->bind_param("i", $_GET['StudentID']);
            
            if ($stmt->execute()) {
                echo "<script>
                    alert('Student record deleted successfully!');
                    window.location.href = 'admin-manage-student.php';
              </script>";
                exit;
            } else {
                echo "Error: " . $stmt->error;
            }
    
            $stmt->close();
        }
    // Check if required fields are filled
    if (!empty($_POST['susername']) && !empty($_POST['Email']) && isset($_POST['Status'])) {
        $susername = $_POST['susername'];
        $Email = $_POST['Email'];
        $Status = $_POST['Status'];

        // Prepare SQL query using placeholders
        $stmt = $conn->prepare("UPDATE student SET username = ?, Email = ?, Status = ? WHERE StudentID = ?");
        $stmt->bind_param("ssii", $susername, $Email, $Status, $_GET['StudentID']); 

        // Execute prepared statement
        if ($stmt->execute()) {
            echo "<script>
                alert('Student record updated successfully!');
                    window.location.href = 'view-student-admin.php?StudentID=" . $_GET['StudentID'] . "';
          </script>";
          exit;
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close statement and connection
        $stmt->close();
    }


    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User</title>
    <link rel="stylesheet" href="css/view-user-admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <?php 
        require 'sidebar-admin.php';
    ?>

    <!-- Main Content -->
    <div class="main-content"> 
        <!-- Header -->
        <div class="header-wrapper"> 
            <div class="header-title"> 
                <span>Admin</span> 
                <h2>Update User</h2> 
            </div> 
            <img src="images/manager.png" alt=""> 
        </div> 
        
        <!-- Form -->
        <div class="form-container">
            <div class="view-user">
                <form action="view-student-admin.php?StudentID=<?php echo htmlspecialchars($row['StudentID']) ?>" method="POST">
                    <label for="StudentID">Student ID: <?php echo htmlspecialchars($row['StudentID']) ?></label>
                    <label for="susername">Username:</label>
                    <input type="text" id="susername" name="susername" placeholder="Enter name" value="<?php echo htmlspecialchars($row['username']) ?>" required>

                    <label for="Email">Email</label>
                    <input type="email" id="Email" name="Email" placeholder="Enter class" value="<?php echo htmlspecialchars($row['Email']) ?>" required>

                    <label for="Status">Status</label>
                    <select id="Status" name="Status" required>
                        <option value="1" <?php echo (htmlspecialchars($row['Status']) == '1') ? 'selected' : ''; ?>>Active</option>
                        <option value="0" <?php echo (htmlspecialchars($row['Status']) == '0') ? 'selected' : ''; ?>>Inactive</option>
                    </select>

                    <div class="buttons">
                        <a class="btn btn-return" href="admin-manage-student.php">Return</a>
                        <input type="submit" class="btn btn-update" value="UPDATE">
                        <input type="submit" name="delete" class="btn btn-delete" value="DELETE">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

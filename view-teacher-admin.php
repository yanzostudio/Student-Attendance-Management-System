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

// Check if TeacherID is set and valid
if (isset($_GET['TeacherID']) && !empty($_GET['TeacherID'])) {
    $teacherID = $_GET['TeacherID'];
    
    // Fetch teacher details
    $sqlTeach = "SELECT * FROM teacher WHERE TeacherID = ?";
    $stmtTeach = $conn->prepare($sqlTeach);
    $stmtTeach->bind_param("i", $teacherID);
    $stmtTeach->execute();
    $resultTeach = $stmtTeach->get_result();
    
    if ($resultTeach->num_rows > 0) {
        $row = $resultTeach->fetch_assoc();
    } else {
        echo "Teacher not found.";
        exit;
    }
} else {
    echo "Invalid TeacherID.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the delete button was clicked
    if (isset($_POST['delete'])) {
        // First, delete related records in the class table
        $stmtClass = $conn->prepare("DELETE FROM class WHERE TeacherID = ?");
        $stmtClass->bind_param("i", $teacherID);
        $stmtClass->execute();
        $stmtClass->close();

        // Now delete the teacher record
        $stmt = $conn->prepare("DELETE FROM teacher WHERE TeacherID = ?");
        $stmt->bind_param("i", $teacherID);

        if ($stmt->execute()) {
            echo "<script>
                alert('Teacher record deleted successfully!');
                window.location.href = 'admin-manage-teacher.php';
            </script>";
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    // Check if required fields are filled
    if (!empty($_POST['tusername']) && !empty($_POST['Email']) && isset($_POST['Status']) && !empty($_POST['phoneNo'])) {
        $tusername = $_POST['tusername'];
        $Email = $_POST['Email'];
        $Status = $_POST['Status'];
        $phoneNo =  $_POST['phoneNo'];

        // Prepare SQL query using placeholders
        $stmt = $conn->prepare("UPDATE teacher SET username = ?, Email = ?, Status = ?, phoneNo = ? WHERE TeacherID = ?");
        $stmt->bind_param("ssisi", $tusername, $Email, $Status, $phoneNo, $teacherID);

        // Execute prepared statement
        if ($stmt->execute()) {
            echo "<script>
                alert('Teacher record updated successfully!');
                window.location.href = 'view-teacher-admin.php?TeacherID=" . $teacherID . "';
            </script>";
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Teacher</title>
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
                <h2>Update Teacher</h2> 
            </div> 
            <img src="images/manager.png" alt=""> 
        </div> 
        
        <!-- Form -->
        <div class="form-container">
            <div class="view-user">
                <form action="view-teacher-admin.php?TeacherID=<?php echo htmlspecialchars($row['TeacherID']) ?>" method="POST">
                    <label for="teacherID">Teacher ID: <?php echo htmlspecialchars($row['TeacherID']) ?></label>
                    <label for="tusername">Username:</label>
                    <input type="text" id="tusername" name="tusername" placeholder="Enter name" value="<?php echo htmlspecialchars($row['username']) ?>" required>

                    <label for="Email">Email</label>
                    <input type="email" id="Email" name="Email" placeholder="Enter email" value="<?php echo htmlspecialchars($row['Email']) ?>" required>

                    <label for="Status">Status</label>
                    <select id="Status" name="Status" required>
                        <option value="1" <?php echo (htmlspecialchars($row['Status']) == '1') ? 'selected' : ''; ?>>Active</option>
                        <option value="0" <?php echo (htmlspecialchars($row['Status']) == '0') ? 'selected' : ''; ?>>Inactive</option>
                    </select>

                    <label>Phone No</label>
                    <input type="tel" name="phoneNo" value="<?php echo htmlspecialchars($row['phoneNo']) ?>" required/>
                    
                    <div class="buttons">
                        <a class="btn btn-return" href="admin-manage-teacher.php">Return</a>
                        <input type="submit" class="btn btn-update" value="UPDATE">
                        <input type="submit" name="delete" class="btn btn-delete" value="DELETE">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

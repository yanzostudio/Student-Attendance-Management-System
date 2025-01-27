<?php
require 'db_config.php';

// Ensure Student_ID is passed in the URL
$studentID = isset($_GET['Student_ID']) ? $_GET['Student_ID'] : null;
if ($studentID === null) {
    echo "Error: Student ID is missing.";
    exit;
}

// Get the student data based on Student_ID
$sqlStud = "SELECT * FROM students WHERE Student_ID = :studentID";
$stmtStud = oci_parse($conn, $sqlStud);
oci_bind_by_name($stmtStud, ":studentID", $studentID);
oci_execute($stmtStud);

$row = oci_fetch_assoc($stmtStud);

// Handle POST requests for updating or deleting student data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if the delete button was clicked
    if (isset($_POST['delete'])) {
        $sqlDelete = "DELETE FROM students WHERE Student_ID = :studentID";
        $stmtDelete = oci_parse($conn, $sqlDelete);
        oci_bind_by_name($stmtDelete, ":studentID", $studentID);
        
        if (oci_execute($stmtDelete)) {
            echo "<script>
                alert('Student record deleted successfully!');
                window.location.href = 'admin-manage-student.php';
              </script>";
            exit;
        } else {
            echo "Error: " . oci_error($stmtDelete);
        }
    }

    // Check if required fields are filled and update the student
    if (!empty($_POST['name']) && !empty($_POST['contactNo']) && !empty($_POST['Email']) && !empty($_POST['dob']) && isset($_POST['gender'])) {
        $name = $_POST['name'];
        $contactNo = $_POST['contactNo'];
        $dob = $_POST['dob'];
        $Email = $_POST['Email'];
        $gender = $_POST['gender'];

        // Prepare SQL query using placeholders for update
        $sqlUpdate = "UPDATE students 
                      SET STUDENT_NAME = :name, 
                          STUDENT_CONTACTNO = :contactNo, 
                          STUDENT_DATEOFBIRTH = TO_DATE(:dob, 'YYYY-MM-DD'), 
                          STUDENT_EMAIL = :email, 
                          STUDENT_GENDER = :gender 
                      WHERE Student_ID = :studentID";
        $stmtUpdate = oci_parse($conn, $sqlUpdate);
        oci_bind_by_name($stmtUpdate, ":name", $name);
        oci_bind_by_name($stmtUpdate, ":dob", $dob);  // Binding the date field correctly
        oci_bind_by_name($stmtUpdate, ":contactNo", $contactNo);
        oci_bind_by_name($stmtUpdate, ":email", $Email);
        oci_bind_by_name($stmtUpdate, ":gender", $gender);
        oci_bind_by_name($stmtUpdate, ":studentID", $studentID);

        // Execute prepared statement
        if (oci_execute($stmtUpdate)) {
            echo "<script>
                alert('Student record updated successfully!');
                window.location.href = 'view-student-admin.php?Student_ID=" . $studentID . "';
              </script>";
            exit;
        } else {
            echo "Error: " . oci_error($stmtUpdate);
        }
    }
}

// Close Oracle connection
oci_free_statement($stmtStud);
oci_close($conn);
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
                <form action="view-student-admin.php?Student_ID=<?php echo htmlspecialchars($row['STUDENT_ID']) ?>" method="POST">
                    <label for="Student_ID">Student ID: <?php echo htmlspecialchars($row['STUDENT_ID']) ?></label>
                    <label for="name">Username:</label>
                    <input type="text" id="name" name="name" placeholder="Enter name" value="<?php echo htmlspecialchars($row['STUDENT_NAME']) ?>" required>

                    <label for="Email">Email</label>
                    <input type="email" id="Email" name="Email" placeholder="Enter email" value="<?php echo htmlspecialchars($row['STUDENT_EMAIL']) ?>" required>

                    <div class="form-group">
                        <label>Contact No</label>
                        <input type="tel"  name="contactNo" value="<?php echo htmlspecialchars($row['STUDENT_CONTACTNO']) ?>" required/>
                    </div>
                    <div class="form-group">
                        <label>DOB</label>
                        <input type="date" name="dob" value="<?php echo htmlspecialchars(date('Y-m-d', strtotime($row['STUDENT_DATEOFBIRTH']))) ?>" required />

                    </div>

                    <div class="form-group">
                        <label for="gender">Gender:</label>
                        <select name="gender" id="gender">
                            <option value="M" <?php echo (htmlspecialchars($row['STUDENT_GENDER']) == 'M') ? 'selected' : ''; ?>>Male</option>
                            <option value="F" <?php echo (htmlspecialchars($row['STUDENT_GENDER']) == 'F') ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>

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

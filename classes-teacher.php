<?php 
require 'db_config.php'; // Ensure proper connection
session_start();

// Check if the teacher is logged in
if (!isset($_SESSION['teacherID'])) {
    header("Location: login.php");
    exit();
}

$teacherID = $_SESSION['teacherID'];
$successMessage = '';


// Query to fetch classes for the logged-in teacher
$sql = "SELECT CLASS_ID, CLASS_NAME FROM CLASSES WHERE TEACHER_ID = :teacherID ORDER BY CLASS_ID DESC";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ":teacherID", $teacherID);
oci_execute($stmt);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Class</title>
        <link rel="stylesheet" href="css/classes-teacher-styles.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            /* Optional: Add styles for success message */
            .notification-message {
                padding: 10px;
                background-color: #4CAF50;
                color: white;
                border-radius: 5px;
                text-align: center;
                margin-top: 20px;
            }
        </style>
    </head>

    <body>
    <?php require 'sidebar-teacher.php'; ?>

        <div class="main-content">
            <div class="header-wrapper">
                <div class="header-title">
                    <span>Teacher</span>
                    <h2>Class</h2>
                </div>
                <img src="images/teacher.png" alt="Teacher">
            </div>
            
            <div class="tabular-wrapper">
                <h3 class="main-title">Classes</h3>

                <!-- Success Message -->
                <?php if (!empty($successMessage)): ?>
                    <div class="notification-message">
                        <?php echo htmlspecialchars($successMessage); ?>
                    </div>
                <?php endif; ?>

                <div class="table-container">
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Bil.</th>
                                <th>Class</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $counter = 1;
                                while ($row = oci_fetch_assoc($stmt)) {
                                    echo "<tr>
                                        <td>" . $counter++ . "</td>
                                        <td>" . htmlspecialchars($row['CLASS_NAME']) . "</td>
                                        <td>
                                            <a href='view-class-teacher.php?classID=" . $row['CLASS_ID'] . "'>View</a> 
                                        </td>
                                    </tr>";
                                }
                                oci_free_statement($stmt);
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>

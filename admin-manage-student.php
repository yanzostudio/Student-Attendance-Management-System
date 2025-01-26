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
$sqlStud = "SELECT * FROM student ORDER BY StudentID DESC";
$resultStud = $conn->query($sqlStud);

?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Class</title>
        <link rel="stylesheet" href="css/admin-manage-student.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>

    <body>
        <!-- sidebar -->
    <?php require 'sidebar-admin.php'; ?>

        <div class="main-content">
            <div class="header-wrapper">
                <div class="header-title">
                    <span>Admin</span>
                    <h2>Student</h2>
                </div>
                <img src="images/manager.png">
            </div>
            
                <div class="tabular-wrapper">
                    <h3 class="main-title">
                        Classes
                    </h3>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Bil.</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Student ID</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                                <tbody>
                        <?php
                        if ($resultStud->num_rows > 0) {
                        $index = 1;
                           while($row = $resultStud->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $index . "</td>";
                                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['StudentID']) . "</td>"; // Assuming `student_id` is a field
                                echo "<td>" . htmlspecialchars($row['Email']) . "</td>"; // Assuming `class` is a field
                                echo "<td>" . "<a href='view-student-admin.php?StudentID=" . $row['StudentID'] . "'>View</a>" . "</td>";
                                echo "</tr>";
                                $index++;
                            }
                        } else {
                            echo "<tr><td colspan='4'>No records found</td></tr>";
                        }

                        $conn->close();
                        ?>
                    </tbody>
                            </thead>
                        </table>
                    </div>
            </div>
        </div>

       
    </body>
</html>

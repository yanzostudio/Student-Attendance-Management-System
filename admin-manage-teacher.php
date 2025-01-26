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
$sqlTeach = "SELECT * FROM teacher ORDER BY TeacherID DESC";
$resultTeach = $conn->query($sqlTeach);

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
                    <h2>Teacher</h2>
                </div>
                <img src="images/manager.png">
            </div>
            
                <div class="tabular-wrapper">
                    <h3 class="main-title">
                        Teacher
                    </h3>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Bil.</th>
                                    <th>Name</th>
                                    <th>Teacher ID</th>
                                    <th>Email</th>
                                    <th>Phone No</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                        <?php
                        if ($resultTeach->num_rows > 0) {
                            $index = 1;
                            while($row = $resultTeach->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $index . "</td>";
                                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['TeacherID']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['Email']) . "</td>"; 
                                echo "<td>" . htmlspecialchars($row['phoneNo']) . "</td>"; 
                                echo "<td>" . "<a href='view-teacher-admin.php?TeacherID=" . $row['TeacherID'] . "'>View</a>" . "</td>";
                                echo "</tr>";
                                $index++;
                            }
                        } else {
                            echo "<tr><td colspan='4'>No records found</td></tr>";
                        }

                        $conn->close();
                        ?>
                    </tbody>
                        </table>
                    </div>
            </div>
        </div>

    </body>
</html>

<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sams_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch schedule data
$sql = "SELECT s.TimeSlot, c.ClassName, t.username as TeacherName 
        FROM schedule s
        JOIN class c ON s.ClassID = c.ClassID
        JOIN teacher t ON s.TeacherID = t.teacherID";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Schedule Page</title>
    <link rel="stylesheet" href="css/admin-schedule-style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
    <?php require 'sidebar-admin.php'; ?>

    <div class="main--content">
        <div class="header--wrapper">
            <div class="header--title">
                <span>Admin</span>
                <h2>View Schedule</h2>
            </div>
            <div class="user--info">
                <img src="images/manager.png" alt="">
            </div>
        </div>

        <div class="tabular--wrapper">
            <h3 class="main--title">CDCS2304C</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Days | Time</th>
                            <th>08:00-10:00</th>
                            <th>10:00-12:00</th>
                            <th>12:00-14:00</th>
                            <th>14:00-16:00</th>
                            <th>16:00-18:00</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Check if there are rows returned
                        if ($result->num_rows > 0) {
                            // Loop through each row and display in table
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$row['ClassName']}</td>
                                    <td>{$row['TimeSlot']}</td>
                                    <td>{$row['TeacherName']}</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No schedule available</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php $conn->close(); ?>
</body>
</html>

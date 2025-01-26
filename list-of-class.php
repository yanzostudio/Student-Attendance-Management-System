<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Class</title>
    <link rel="stylesheet" href="css/list-of-class-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <!-- Sidebar -->
    <?php require 'sidebar-admin.php'; ?>

    <div class="main-content">
        <div class="header-wrapper">
            <div class="header-title">
                <span>Admin</span>
                <h2>Attendance</h2>
            </div>
            <img src="images/manager.png" alt="">
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
                            <th>Class</th>
                            <th>Action</th>
                            <th>Schedule</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Database connection
                        $servername = "localhost";
                        $username = "root"; // or your DB username
                        $password = ""; // or your DB password
                        $dbname = "sams_db"; // your DB name

                        // Create connection
                        $conn = new mysqli($servername, $username, $password, $dbname);

                        // Check connection
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        // Query to get class data
                        $sql = "SELECT c.ClassID, c.ClassName FROM class c";
                        $result = $conn->query($sql);

                        // Check if any classes exist
                        if ($result->num_rows > 0) {
                            // Output data for each row
                            $bil = 1;
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>" . $bil++ . "</td>
                                        <td>" . $row["ClassName"] . "</td>
                                        <td><a href='view-class-admin.php?classID=" . $row["ClassID"] . "'>View</a></td>
                                        <td><a href='schedule-admin.php?classID=" . $row["ClassID"] . "'>View</a></td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No classes available</td></tr>";
                        }

                        // Close connection
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

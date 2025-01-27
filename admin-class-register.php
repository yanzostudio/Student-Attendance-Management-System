<?php 
require 'db_config.php'; // Ensure proper connection

// Handle POST request to register a new class
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if all required fields are set
    if (isset($_POST['class_name'], $_POST['class_duration'], $_POST['class_capacity'], $_POST['class_starttime'], $_POST['class_endtime'], $_POST['class_platform'], $_POST['subject_id'], $_POST['teacher_id'])) {
        
        $class_name = $_POST['class_name'];
        $class_duration = $_POST['class_duration'];
        $class_capacity = $_POST['class_capacity'];
        $class_starttime = $_POST['class_starttime'];
        $class_endtime = $_POST['class_endtime'];
        $class_platform = $_POST['class_platform'];
        $subject_id = $_POST['subject_id'];
        $teacher_id = $_POST['teacher_id'];

        // Convert start and end time to Oracle's DATE format
        $class_starttime = date("Y-m-d H:i:s", strtotime($class_starttime)); // Format: YYYY-MM-DD HH:MM:SS
        $class_endtime = date("Y-m-d H:i:s", strtotime($class_endtime));     // Format: YYYY-MM-DD HH:MM:SS

        // SQL query to insert data into 'classes' table
        $sqlInsert = "INSERT INTO classes (CLASS_NAME, CLASS_DURATION, CLASS_CAPACITY, CLASS_STARTTIME, CLASS_ENDTIME, CLASS_PLATFORM, SUBJECT_ID, TEACHER_ID)
                      VALUES (:class_name, :class_duration, :class_capacity, TO_DATE(:class_starttime, 'YYYY-MM-DD HH24:MI:SS'), TO_DATE(:class_endtime, 'YYYY-MM-DD HH24:MI:SS'), :class_platform, :subject_id, :teacher_id)";
        
        $stmtInsert = oci_parse($conn, $sqlInsert);
        
        // Bind the input values to the SQL query
        oci_bind_by_name($stmtInsert, ":class_name", $class_name);
        oci_bind_by_name($stmtInsert, ":class_duration", $class_duration);
        oci_bind_by_name($stmtInsert, ":class_capacity", $class_capacity);
        oci_bind_by_name($stmtInsert, ":class_starttime", $class_starttime);
        oci_bind_by_name($stmtInsert, ":class_endtime", $class_endtime);
        oci_bind_by_name($stmtInsert, ":class_platform", $class_platform);
        oci_bind_by_name($stmtInsert, ":subject_id", $subject_id);
        oci_bind_by_name($stmtInsert, ":teacher_id", $teacher_id);

        // Execute the query
        if (oci_execute($stmtInsert)) {
            echo "<script>
                    alert('Class registered successfully!');
                    window.location.href = 'admin-class-register.php'; 
                  </script>";
        } else {
            echo "Error: " . oci_error($stmtInsert);
        }
    }
}

// Fetch all classes for display
$sqlSelect = "SELECT * FROM classes";
$stmtSelect = oci_parse($conn, $sqlSelect);

// Execute the query and check for errors
if (!oci_execute($stmtSelect)) {
    $error = oci_error($stmtSelect);
    echo "Error executing query: " . $error['message'];
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin</title>
    <link rel="stylesheet" href="css/admin-course-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- Sidebar -->
<?php require 'sidebar-admin.php'; ?>

<div class="main-content">
    <div class="header-wrapper">
        <div class="header-title">
            <span>Admin</span>
            <h2>Register New Class</h2>
        </div>
        <img src="images/manager.png">
    </div>

    <div class="wrapper">
        <div class="content-wrapper">
            <!-- Class Registration Form -->
            <form class="class-form" id="class-form" method="POST">
                <div class="form-group">
                    <label for="class-name">Class Name:</label>
                    <input type="text" id="class-name" name="class_name" placeholder="Enter class name" required>
                </div>

                <div class="form-group">
                    <label for="class-duration">Class Duration (hours):</label>
                    <input type="number" id="class-duration" name="class_duration" placeholder="Enter class duration in hours" required>
                </div>

                <div class="form-group">
                    <label for="class-capacity">Class Capacity:</label>
                    <input type="number" id="class-capacity" name="class_capacity" placeholder="Enter class capacity" required>
                </div>

                <div class="form-group">
                    <label for="class-starttime">Class Start Time:</label>
                    <input type="datetime-local" id="class-starttime" name="class_starttime" required>
                </div>

                <div class="form-group">
                    <label for="class-endtime">Class End Time:</label>
                    <input type="datetime-local" id="class-endtime" name="class_endtime" required>
                </div>

                <div class="form-group">
                    <label for="class-platform">Class Platform:</label>
                    <input type="text" id="class-platform" name="class_platform" placeholder="Enter class platform" required>
                </div>

                <div class="form-group">
                    <label for="subject-id">Subject ID:</label>
                    <input type="number" id="subject-id" name="subject_id" placeholder="Enter subject ID" required>
                </div>

                <div class="form-group">
                    <label for="teacher-id">Teacher ID:</label>
                    <input type="number" id="teacher-id" name="teacher_id" placeholder="Enter teacher ID" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn-submit" id="register-button">Register Class</button>
                </div>
            </form>

            <!-- Table to display classes -->
            <h3>Existing Classes</h3>
            <table id="class-table" border="1" class="table">
                <thead>
                    <tr>
                        <th>Class Name</th>
                        <th>Class Duration</th>
                        <th>Class Capacity</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Platform</th>
                        <th>Subject ID</th>
                        <th>Teacher ID</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Ensure the resource is valid before fetching rows
                    if ($stmtSelect) {
                        while ($row = oci_fetch_assoc($stmtSelect)) {
                            echo "<tr>
                                <td>" . htmlspecialchars($row['CLASS_NAME']) . "</td>
                                <td>" . htmlspecialchars($row['CLASS_DURATION']) . "</td>
                                <td>" . htmlspecialchars($row['CLASS_CAPACITY']) . "</td>
                                <td>" . htmlspecialchars($row['CLASS_STARTTIME']) . "</td>
                                <td>" . htmlspecialchars($row['CLASS_ENDTIME']) . "</td>
                                <td>" . htmlspecialchars($row['CLASS_PLATFORM']) . "</td>
                                <td>" . htmlspecialchars($row['SUBJECT_ID']) . "</td>
                                <td>" . htmlspecialchars($row['TEACHER_ID']) . "</td>
                                <td><a href='view-class-admin.php?CLASS_ID=" . htmlspecialchars($row['CLASS_ID']) . "'>View</a></td> 
                            </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="js/scripts.js"></script>
</body>
</html>

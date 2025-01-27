<?php 
require 'db_config.php'; // Ensure proper connection

// Handle POST request to register a new class
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if all required fields are set
    if (isset($_POST['subject_name'], $_POST['subject_description'], $_POST['subject_LOC'], $_POST['subject_difficulty'])) {
        
        $subject_name = $_POST['subject_name'];
        $subject_description = $_POST['subject_description'];
        $subject_LOC = $_POST['subject_LOC'];
        $subject_difficulty = $_POST['subject_difficulty'];
 


        // SQL query to insert data into 'classes' table
        $sqlInsert = "INSERT INTO SUBJECTS (SUBJECT_NAME, SUBJECT_DESCRIPTION, SUBJECT_LISTOFCONTENT, SUBJECT_DIFFICULTY)
                      VALUES (:subject_name, :subject_description, :subject_LOC, :subject_difficulty)";
        
        $stmtInsert = oci_parse($conn, $sqlInsert);
        
        // Bind the input values to the SQL query
        oci_bind_by_name($stmtInsert, ":subject_name", $subject_name);
        oci_bind_by_name($stmtInsert, ":subject_description", $subject_description);
        oci_bind_by_name($stmtInsert, ":subject_LOC", $subject_LOC);
        oci_bind_by_name($stmtInsert, ":subject_difficulty", $subject_difficulty);

        // Execute the query
        if (oci_execute($stmtInsert)) {
            echo "<script>
                    alert('Subjects registered successfully!');
                    window.location.href = 'admin-subject-register.php'; 
                  </script>";
        } else {
            echo "Error: " . oci_error($stmtInsert);
        }
    }
}

// Fetch all classes for display
$sqlSelect = "SELECT * FROM subjects";
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
            <h2>Register New Subjects</h2>
        </div>
        <img src="images/manager.png">
    </div>

    <div class="wrapper">
        <div class="content-wrapper">
            <!-- Class Registration Form -->
            <form class="class-form" id="class-form" method="POST">
                <div class="form-group">
                    <label for="subject_name">Subject Name:</label>
                    <input type="text" id="subject_name" name="subject_name" required>
                </div>

                <div class="form-group">
                    <label for="subject_description">Subject Description:</label>
                    <input type="text" id="subject_description" name="subject_description"  required>
                </div>
                <div class="form-group">
                    <label for="subject_LOC">Subject List of Content:</label>
                    <input type="text" id="subject_LOC" name="subject_LOC" required>
                </div>
                <div class="form-group">
                    <label for="subject_difficulty">Subject Difficulty (1-10):</label>
                    <input type="number" id="subject_difficulty" name="subject_difficulty"  required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn-submit" id="register-button">Register Subject</button>
                </div>
            </form>

            <!-- Table to display classes -->
            <h3>Existing Subjects</h3>
            <table id="class-table" border="1" class="table">
                <thead>
                    <tr>
                        <th>Subject ID</th>
                        <th>Subject NAME</th>
                        <th>Subject DESCRIPTION</th>
                        <th>Subject Difficulty</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Ensure the resource is valid before fetching rows
                    if ($stmtSelect) {
                        while ($row = oci_fetch_assoc($stmtSelect)) {
                            echo "<tr>
                                <td>" . htmlspecialchars($row['SUBJECT_ID']) . "</td>
                                <td>" . htmlspecialchars($row['SUBJECT_NAME']) . "</td>
                                <td>" . htmlspecialchars($row['SUBJECT_DESCRIPTION']) . "</td>
                                <td>" . htmlspecialchars($row['SUBJECT_DIFFICULTY']) . "</td>
                                <td><a href='view-subject-admin.php?SUBJECT_ID=" . htmlspecialchars($row['SUBJECT_ID']) . "'>View</a></td> 
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

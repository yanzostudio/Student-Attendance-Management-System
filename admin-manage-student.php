<?php
require 'db_config.php';

// SQL query to fetch student data
$sqlStud = "SELECT * FROM students ORDER BY Student_ID DESC";
$stmtStud = oci_parse($conn, $sqlStud);
oci_execute($stmtStud);

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
            <h3 class="main-title">Classes</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Bil.</th>
                            <th>Name</th>
                            <th>Contact No</th>
                            <th>Student ID</th>
                            <th>Email</th>
                            <th>DOB</th>
                            <th>Gender</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    // Fetch the rows and display them
                    $index = 1;
                    while ($row = oci_fetch_assoc($stmtStud)) {
                        echo "<tr>";
                        echo "<td>" . $index . "</td>";
                        echo "<td>" . htmlspecialchars($row['STUDENT_NAME']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['STUDENT_CONTACTNO']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['STUDENT_ID']) . "</td>"; // Assuming `student_id` is a field
                        echo "<td>" . htmlspecialchars($row['STUDENT_EMAIL']) . "</td>"; 
                        echo "<td>" . htmlspecialchars($row['STUDENT_DATEOFBIRTH']) . "</td>"; 
                        echo "<td>" . htmlspecialchars($row['STUDENT_GENDER']) . "</td>"; 
                        echo "<td>" . "<a href='view-student-admin.php?Student_ID=" . $row['STUDENT_ID'] . "'>View</a>" . "</td>";
                        echo "</tr>";
                        $index++;
                    }

                    // Check if no records were found
                    if ($index == 1) {
                        echo "<tr><td colspan='6'>No records found</td></tr>";
                    }

                    // Close Oracle connection
                    oci_free_statement($stmtStud);
                    oci_close($conn);
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>

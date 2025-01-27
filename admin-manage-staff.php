<?php
require 'db_config.php';

// SQL query to fetch staff data (excluding teachers)
$sqlStaff = "SELECT 
                s.STAFF_NAME, 
                s.STAFF_CONTACTNO, 
                s.STAFF_EMAIL, 
                s.STAFF_POSITION, 
                s.STAFF_HIREDATE, 
                s.STAFF_ID
             FROM staffs s 
             LEFT JOIN teachers t ON s.STAFF_ID = t.STAFF_ID
             WHERE t.STAFF_ID IS NULL 
             ORDER BY s.STAFF_ID DESC";
$stmtStaff = oci_parse($conn, $sqlStaff);
oci_execute($stmtStaff);

// SQL query to fetch teacher data by joining the staffs and teacher tables
$sqlTeacher = "SELECT 
                    s.STAFF_NAME, 
                    s.STAFF_CONTACTNO, 
                    s.STAFF_EMAIL, 
                    s.STAFF_POSITION, 
                    s.STAFF_HIREDATE, 
                    t.TEACHER_SPECIALIZATION, 
                    t.TEACHER_ACHIEVEMENT, 
                    t.TEACHER_STATUS, 
                    s.STAFF_ID 
                FROM staffs s 
                JOIN teachers t ON s.STAFF_ID = t.STAFF_ID 
                ORDER BY s.STAFF_ID DESC";
$stmtTeacher = oci_parse($conn, $sqlTeacher);
oci_execute($stmtTeacher);
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
    <!-- Sidebar -->
    <?php require 'sidebar-admin.php'; ?>

    <div class="main-content">
        <div class="header-wrapper">
            <div class="header-title">
                <span>Admin</span>
                <h2>Manage Staff & Teachers</h2>
            </div>
            <img src="images/manager.png">
        </div>

        <!-- Staff Table (without teachers) -->
        <div class="tabular-wrapper">
            <h3 class="main-title">Staff</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Bil.</th>
                            <th>Name</th>
                            <th>Contact No</th>
                            <th>Staff ID</th>
                            <th>Email</th>
                            <th>Position</th>
                            <th>Hire Date</th>

                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    // Fetch the rows and display them for staff
                    $index = 1;
                    while ($row = oci_fetch_assoc($stmtStaff)) {
                        echo "<tr>";
                        echo "<td>" . $index . "</td>";
                        echo "<td>" . htmlspecialchars($row['STAFF_NAME']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['STAFF_CONTACTNO']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['STAFF_ID']) . "</td>"; // Assuming `staff_id` is a field
                        echo "<td>" . htmlspecialchars($row['STAFF_EMAIL']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['STAFF_POSITION']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['STAFF_HIREDATE']) . "</td>";
                        echo "</tr>";
                        $index++;
                    }

                    // Check if no staff records were found
                    if ($index == 1) {
                        echo "<tr><td colspan='8'>No staff found</td></tr>";
                    }

                    // Close the staff query
                    oci_free_statement($stmtStaff);
                    ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Teachers Table -->
        <div class="tabular-wrapper">
            <h3 class="main-title">Teachers</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Bil.</th>
                            <th>Name</th>
                            <th>Contact No</th>
                            <th>Staff ID</th>
                            <th>Email</th>
                            <th>Position</th>
                            <th>Hire Date</th>
                            <th>Specialization</th>
                            <th>Achievement</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    // Fetch the rows and display them for teachers
                    $index = 1;
                    while ($row = oci_fetch_assoc($stmtTeacher)) {
                        echo "<tr>";
                        echo "<td>" . $index . "</td>";
                        echo "<td>" . htmlspecialchars($row['STAFF_NAME']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['STAFF_CONTACTNO']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['STAFF_ID']) . "</td>"; // Assuming `staff_id` is a field
                        echo "<td>" . htmlspecialchars($row['STAFF_EMAIL']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['STAFF_POSITION']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['STAFF_HIREDATE']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['TEACHER_SPECIALIZATION']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['TEACHER_ACHIEVEMENT']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['TEACHER_STATUS']) . "</td>";
                        echo "</tr>";
                        $index++;
                    }

                    // Check if no teacher records were found
                    if ($index == 1) {
                        echo "<tr><td colspan='11'>No teachers found</td></tr>";
                    }

                    // Close the teacher query
                    oci_free_statement($stmtTeacher);
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>

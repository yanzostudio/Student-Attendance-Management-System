<?php
require 'db_config.php'; // Ensure proper connection
session_start();
if ($_SESSION['teacherID'] == null) {
    header("Location: login.php");
    exit();
}

// Get the class ID (you can pass it via GET/POST or SESSION)
$classID = $_GET['classID'] ?? null;

// Fetch class and student data
$classData = [];
$students = [];

if ($classID) {
    // Fetch class details
    $classQuery = "SELECT * FROM CLASSES WHERE CLASS_ID = :classID";
    $classStmt = oci_parse($conn, $classQuery);
    oci_bind_by_name($classStmt, ':classID', $classID);

    if (oci_execute($classStmt)) {
        $classData = oci_fetch_assoc($classStmt);
    }
    oci_free_statement($classStmt);

    // Fetch students in the class
    $studentQuery = "
        SELECT s.STUDENT_ID, s.STUDENT_NAME, s.STUDENT_EMAIL
        FROM ENROLLS e
        JOIN STUDENTS s ON e.STUDENT_ID = s.STUDENT_ID
        WHERE e.CLASS_ID = :classID
    ";
    $studentStmt = oci_parse($conn, $studentQuery);
    oci_bind_by_name($studentStmt, ':classID', $classID);

    if (oci_execute($studentStmt)) {
        while ($row = oci_fetch_assoc($studentStmt)) {
            $students[] = $row;
        }
    }
    oci_free_statement($studentStmt);
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Class</title>
        <link rel="stylesheet" href="css/classes-teacher-styles.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body>
        <?php require 'sidebar-teacher.php';?>

        <div class="main-content">
            <div class="header-wrapper">
                <div class="header-title">
                    <span>Teacher</span>
                    <h2>Class</h2>
                </div>
                <img src="images/teacher.png" alt="">
            </div>
            
            <div class="tabular-wrapper">
                <h3 class="main-title">
                    <?= htmlspecialchars($classData['CLASS_NAME'] ?? 'Unknown Class') ?>
                </h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Bil.</th>
                                <th>Name</th>
                                <th>Student ID</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($students)): ?>
                                <?php foreach ($students as $index => $student): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($student['STUDENT_NAME']) ?></td>
                                        <td><?= htmlspecialchars($student['STUDENT_ID']) ?></td>
                                        <td><?= htmlspecialchars($student['STUDENT_EMAIL']) ?>   </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">No students found for this class.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="button-container">
                    <a href="classes-teacher.php" class="btn return-btn">Return</a>
                </div>
            </div>
        </div>
    </body>
</html>
<?php
oci_close($conn);
?>

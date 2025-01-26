<?php
require 'db_config.php'; // Ensure proper connection
session_start();
if (($_SESSION['teacherID']) == null) {
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
    $classQuery = $conn->prepare("SELECT * FROM class WHERE ClassID = ?");
    if ($classQuery) {
        $classQuery->bind_param('i', $classID);
        $classQuery->execute();
        $classResult = $classQuery->get_result();
        if ($classResult->num_rows > 0) {
            $classData = $classResult->fetch_assoc();
        }
    }

    // Fetch students in the class
    $studentQuery = $conn->prepare("
        SELECT s.StudentID, s.username, a.AttendanceStatus
        FROM enroll e
        JOIN student s ON e.StudentID = s.StudentID
        LEFT JOIN attendance a ON a.StudentID = s.StudentID AND a.ClassID = e.ClassID
        WHERE e.ClassID = ?
    ");
    if ($studentQuery) {
        $studentQuery->bind_param('i', $classID);
        $studentQuery->execute();
        $studentResult = $studentQuery->get_result();
        while ($row = $studentResult->fetch_assoc()) {
            $students[] = $row;
        }
        $studentQuery->close();
    }
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
                    <?= htmlspecialchars($classData['ClassName'] ?? 'Unknown Class') ?>
                </h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Bil.</th>
                                <th>Name</th>
                                <th>Student ID</th>
                                <th>Verify Attendance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($students)): ?>
                                <?php foreach ($students as $index => $student): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($student['username']) ?></td>
                                        <td><?= htmlspecialchars($student['StudentID']) ?></td>
                                        <td>
                                            <span>
                                                <?= $student['AttendanceStatus'] == 1 ? 'Present' : 'Absent' ?>
                                            </span>
                                        </td>
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
                    <a href="generate-report-teacher.php?classID=<?= $classID ?>" class="btn report-btn">Generate Report</a>
                </div>
            </div>
        </div>
    </body>
</html>
<?php
if (isset($classQuery) && $classQuery instanceof mysqli_stmt) {
    $classQuery->close();
}
$conn->close();
?>

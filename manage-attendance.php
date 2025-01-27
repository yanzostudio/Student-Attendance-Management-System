<?php
require 'db_config.php';
session_start();

if (empty($_SESSION['teacherID'])) {
    header("Location: login.php");
    exit();
}

$teacherID = $_SESSION['teacherID'];

// Fetch classes for the teacher
$classQuery = "SELECT CLASS_ID, CLASS_NAME FROM CLASSES WHERE TEACHER_ID = :teacherID";
$classStmt = oci_parse($conn, $classQuery);
oci_bind_by_name($classStmt, ":teacherID", $teacherID);
oci_execute($classStmt);

$classes = [];
while ($row = oci_fetch_assoc($classStmt)) {
    $classes[] = $row;
}
oci_free_statement($classStmt);

// Delete all attendance for a specific date and class
if (isset($_POST['deleteAll']) && !empty($_POST['classid']) && !empty($_POST['dateTime'])) {
    $classID = $_POST['classid'];
    $dateTime = $_POST['dateTime'];

    $deleteAllQuery = "
        DELETE FROM ATTENDANCE 
        WHERE CLASS_ID = :classID 
          AND ATTENDANCE_TIME = TO_DATE(:dateTime, 'YYYY-MM-DD')";
    $deleteAllStmt = oci_parse($conn, $deleteAllQuery);
    oci_bind_by_name($deleteAllStmt, ":classID", $classID);
    oci_bind_by_name($deleteAllStmt, ":dateTime", $dateTime);
    oci_execute($deleteAllStmt);

    echo "<script>alert('All attendance records for the selected date and class have been deleted successfully!');</script>";
}

// Fetch attendance if class and date are selected
$attendances = [];
if (!empty($_GET['classid']) && !empty($_GET['dateTime'])) {
    $classID = $_GET['classid'];
    $dateTime = $_GET['dateTime'];

    $attendanceQuery = "
        SELECT s.STUDENT_ID, s.STUDENT_NAME, 
               NVL(TO_CHAR(a.ATTENDANCE_TIME, 'YYYY-MM-DD'), 'N/A') AS ATTENDANCE_TIME,
               CASE WHEN a.ATTENDED = 'Y' THEN 'Yes' ELSE 'No' END AS ATTENDED
        FROM STUDENTS s
        LEFT JOIN ATTENDANCE a ON s.STUDENT_ID = a.STUDENT_ID 
                              AND a.CLASS_ID = :classID 
                              AND a.ATTENDANCE_TIME = TO_DATE(:dateTime, 'YYYY-MM-DD')
        WHERE s.STUDENT_ID IN (
            SELECT STUDENT_ID FROM ENROLLS WHERE CLASS_ID = :classID
        )
        ORDER BY s.STUDENT_ID";
    $stmt = oci_parse($conn, $attendanceQuery);
    oci_bind_by_name($stmt, ":classID", $classID);
    oci_bind_by_name($stmt, ":dateTime", $dateTime);
    oci_execute($stmt);

    while ($row = oci_fetch_assoc($stmt)) {
        $attendances[] = $row;
    }
    oci_free_statement($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Attendance</title>
    <link rel="stylesheet" href="css/manage-attendance.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php require 'sidebar-teacher.php'; ?>
    <div class="main-content">
        <div class="header-wrapper">
            <div class="header-title-container">
                <span>Teacher</span>
                <h2>Manage Attendance</h2>
            </div>
            <img src="images/teacher.png">
        </div>
        <div class="add-course-container">
            <a href="add-attendance-teacher.php" class="add-attendance-button">Add Attendance</a>
        </div>

        <form method="GET" action="manage-attendance.php">
            <label for="classid">Class:</label>
            <select id="classid" name="classid" required>
                <option value="" disabled selected>Select Class</option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?= htmlspecialchars($class['CLASS_ID']) ?>" <?= (isset($_GET['classid']) && $_GET['classid'] == $class['CLASS_ID']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($class['CLASS_NAME']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="dateTime">Date:</label>
            <input type="date" id="dateTime" name="dateTime" value="<?= htmlspecialchars($_GET['dateTime'] ?? '') ?>" required>

            <button type="submit">View Attendance</button>
        </form>

        <?php if (!empty($attendances)): ?>
            <form method="POST" action="manage-attendance.php">
                <input type="hidden" name="classid" value="<?= htmlspecialchars($_GET['classid']) ?>">
                <input type="hidden" name="dateTime" value="<?= htmlspecialchars($_GET['dateTime']) ?>">
                <button type="submit" name="deleteAll" class="delete-all-button" onclick="return confirm('Are you sure you want to delete all attendance records for this class and date?')">Delete Attendance</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Attendance Time</th>
                        <th>Attended</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendances as $attendance): ?>
                        <tr>
                            <td><?= htmlspecialchars($attendance['STUDENT_ID']) ?></td>
                            <td><?= htmlspecialchars($attendance['STUDENT_NAME']) ?></td>
                            <td><?= htmlspecialchars($attendance['ATTENDANCE_TIME']) ?></td>
                            <td><?= htmlspecialchars($attendance['ATTENDED']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif (isset($_GET['classid']) && isset($_GET['dateTime'])): ?>
            <p>No attendance records found for the selected class and date.</p>
        <?php endif; ?>
    </div>
</body>
</html>

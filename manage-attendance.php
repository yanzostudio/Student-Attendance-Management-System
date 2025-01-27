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

// Delete specific attendance record
if (isset($_GET['delete']) && isset($_GET['studentID']) && isset($_GET['classid']) && isset($_GET['dateTime'])) {
    $studentID = $_GET['studentID'];
    $classID = $_GET['classid'];
    $dateTime = $_GET['dateTime'];

    $deleteQuery = "
        DELETE FROM ATTENDANCE 
        WHERE STUDENT_ID = :studentID 
          AND CLASS_ID = :classID 
          AND ATTENDANCE_TIME = TO_DATE(:dateTime, 'YYYY-MM-DD')";
    $deleteStmt = oci_parse($conn, $deleteQuery);
    oci_bind_by_name($deleteStmt, ":studentID", $studentID);
    oci_bind_by_name($deleteStmt, ":classID", $classID);
    oci_bind_by_name($deleteStmt, ":dateTime", $dateTime);
    oci_execute($deleteStmt);

    echo "<script>alert('Attendance record deleted successfully!');</script>";
    header("Location: manage-attendance.php?classid=$classID&dateTime=$dateTime");
    exit();
}

// Fetch attendance if class and date are selected
$attendances = [];
if (!empty($_GET['classid']) && !empty($_GET['dateTime'])) {
    $classID = $_GET['classid'];
    $dateTime = $_GET['dateTime'];

    $attendanceQuery = "
        SELECT a.ATTENDANCE_TIME, a.STUDENT_ID, s.STUDENT_NAME
        FROM ATTENDANCE a
        JOIN STUDENTS s ON a.STUDENT_ID = s.STUDENT_ID
        WHERE a.CLASS_ID = :classID AND a.ATTENDANCE_TIME = TO_DATE(:dateTime, 'YYYY-MM-DD')";
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
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Attendance Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendances as $attendance): ?>
                        <tr>
                            <td><?= htmlspecialchars($attendance['STUDENT_ID']) ?></td>
                            <td><?= htmlspecialchars($attendance['STUDENT_NAME']) ?></td>
                            <td><?= htmlspecialchars($attendance['ATTENDANCE_TIME']) ?></td>
                            <td>
                                <a href="manage-attendance.php?delete=true&studentID=<?= htmlspecialchars($attendance['STUDENT_ID']) ?>&classid=<?= htmlspecialchars($_GET['classid']) ?>&dateTime=<?= htmlspecialchars($_GET['dateTime']) ?>" 
                                   onclick="return confirm('Are you sure you want to delete this attendance record?')" 
                                   class="add-attendance-button">
                                   Delete
                                </a>
                            </td>
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

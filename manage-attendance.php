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

// Fetch distinct dates with attendance records
$dates = [];
if (!empty($_GET['classid'])) {
    $classID = $_GET['classid'];

    $dateQuery = "
        SELECT DISTINCT TO_CHAR(ATTENDANCE_TIME, 'YYYY-MM-DD') AS ATTENDANCE_DATE 
        FROM ATTENDANCE 
        WHERE CLASS_ID = :classID
        ORDER BY TO_DATE(TO_CHAR(ATTENDANCE_TIME, 'YYYY-MM-DD'), 'YYYY-MM-DD')";
    $dateStmt = oci_parse($conn, $dateQuery);
    oci_bind_by_name($dateStmt, ":classID", $classID);
    oci_execute($dateStmt);

    while ($row = oci_fetch_assoc($dateStmt)) {
        $dates[] = $row['ATTENDANCE_DATE'];
    }
    oci_free_statement($dateStmt);
}

// Fetch attendance records for a specific date
$attendances = [];
if (!empty($_GET['classid']) && !empty($_GET['date'])) {
    $classID = $_GET['classid'];
    $date = $_GET['date'];

    $attendanceQuery = "
        SELECT s.STUDENT_ID, s.STUDENT_NAME, 
               NVL(TO_CHAR(a.ATTENDANCE_TIME, 'YYYY-MM-DD HH24:MI:SS'), 'N/A') AS ATTENDANCE_TIME,
               CASE WHEN a.ATTENDED = 'Y' THEN 'Yes' ELSE 'No' END AS ATTENDED
        FROM STUDENTS s
        LEFT JOIN ATTENDANCE a ON s.STUDENT_ID = a.STUDENT_ID 
                              AND a.CLASS_ID = :classID 
                              AND TO_CHAR(a.ATTENDANCE_TIME, 'YYYY-MM-DD') = :attendanceDate
        WHERE s.STUDENT_ID IN (
            SELECT STUDENT_ID FROM ENROLLS WHERE CLASS_ID = :classID
        )
        ORDER BY s.STUDENT_ID";
    $stmt = oci_parse($conn, $attendanceQuery);
    oci_bind_by_name($stmt, ":classID", $classID);
    oci_bind_by_name($stmt, ":attendanceDate", $date);
    oci_execute($stmt);

    while ($row = oci_fetch_assoc($stmt)) {
        $attendances[] = $row;
    }
    oci_free_statement($stmt);
}

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_date']) && isset($_GET['classid'])) {
    $classID = $_GET['classid'];
    $dateToDelete = $_POST['delete_date'];

    $deleteQuery = "
        DELETE FROM ATTENDANCE 
        WHERE CLASS_ID = :classID 
        AND TO_CHAR(ATTENDANCE_TIME, 'YYYY-MM-DD') = :dateToDelete";
    $deleteStmt = oci_parse($conn, $deleteQuery);
    oci_bind_by_name($deleteStmt, ":classID", $classID);
    oci_bind_by_name($deleteStmt, ":dateToDelete", $dateToDelete);
    oci_execute($deleteStmt);
    oci_free_statement($deleteStmt);

    // Redirect to avoid resubmission
    header("Location: manage-attendance.php?classid=$classID");
    exit();
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
            <button type="submit">View Dates</button>
        </form>

        <?php if (!empty($dates) && empty($_GET['date'])): ?>
    <h3>Date</h3>
    <ul>
        <?php foreach ($dates as $date): ?>
            <li>
                <a href="manage-attendance.php?classid=<?= htmlspecialchars($_GET['classid']) ?>&date=<?= htmlspecialchars($date) ?>">
                    <?= htmlspecialchars($date) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php elseif (isset($_GET['classid']) && empty($dates)): ?>
    <p>No attendance dates found for the selected class.</p>
<?php endif; ?>

<?php if (!empty($attendances)): ?>
    <button onclick="window.location.href='manage-attendance.php?classid=<?= htmlspecialchars($_GET['classid']) ?>';">Back to Dates</button>
    <h3>Attendance Records</h3>
    <form method="POST" action="manage-attendance.php?classid=<?= htmlspecialchars($_GET['classid']) ?>">
        <input type="hidden" name="delete_date" value="<?= htmlspecialchars($_GET['date']) ?>">
        <button type="submit" onclick="return confirm('Are you sure you want to delete all attendance records for this date?');">Delete Attendance</button>
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
<?php elseif (isset($_GET['date'])): ?>
    <button onclick="window.location.href='manage-attendance.php?classid=<?= htmlspecialchars($_GET['classid']) ?>';">Back to Dates</button>
    <p>No attendance records found for the selected date.</p>
<?php endif; ?>

    </div>
</body>
</html>

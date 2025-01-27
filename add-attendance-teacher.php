<?php
require 'db_config.php'; // Ensure Oracle DB connection
session_start();

// Check if the user is logged in
if (!isset($_SESSION['teacherID'])) {
    header("Location: login.php");
    exit();
}

$teacherID = $_SESSION['teacherID'];

// Fetch all classes assigned to the teacher
$query = "SELECT CLASS_ID, CLASS_NAME FROM CLASSES WHERE TEACHER_ID = :teacherID";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ":teacherID", $teacherID);
oci_execute($stmt);

$classes = [];
while ($row = oci_fetch_assoc($stmt)) {
    $classes[] = $row;
}
oci_free_statement($stmt);

$successMessage = ''; // Variable to store success message
$errorMessage = '';   // Variable to store error message

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $ClassID = $_POST['classid'];
    $dateTime = $_POST['dateTime'];

    // Get all students enrolled in the selected class
    $enrollQuery = "SELECT STUDENT_ID FROM ENROLLS WHERE CLASS_ID = :classID";
    $stmt = oci_parse($conn, $enrollQuery);
    oci_bind_by_name($stmt, ":classID", $ClassID);
    oci_execute($stmt);

    $students = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $students[] = $row['STUDENT_ID'];
    }
    oci_free_statement($stmt);

    // Check if there are students enrolled in this class
    if (count($students) > 0) {
        $attendanceQuery = "INSERT INTO ATTENDANCE (CLASS_ID, STUDENT_ID, ATTENDANCE_TIME, ATTENDED) VALUES (:classID, :studentID, TO_DATE(:dateTime, 'YYYY-MM-DD\"T\"HH24:MI'), 'Y')";
        $attendanceStmt = oci_parse($conn, $attendanceQuery);

        foreach ($students as $studentID) {
            oci_bind_by_name($attendanceStmt, ":classID", $ClassID);
            oci_bind_by_name($attendanceStmt, ":studentID", $studentID);
            oci_bind_by_name($attendanceStmt, ":dateTime", $dateTime);

            if (!oci_execute($attendanceStmt)) {
                $e = oci_error($attendanceStmt);
                $errorMessage = "Error inserting attendance for student ID " . $studentID . ": " . $e['message'];
                break;
            }
        }

        if (empty($errorMessage)) {
            $successMessage = 'Attendance added successfully!';
        }

        oci_free_statement($attendanceStmt);
    } else {
        $errorMessage = "No students enrolled in this class.";
    }
}

oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Attendance</title>
    <link rel="stylesheet" href="css/add-course-teacher.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php require 'sidebar-teacher.php'; ?>

<div class="main-content">
    <div class="header-wrapper">
        <div class="header-title">
            <span>Teacher</span>
            <h2>Add Attendance</h2>
        </div>
        <img src="images/teacher.png">
    </div>

    <div class="container">
        <h3 class="main-title">Add a New Attendance</h3>

        <!-- Notification Message -->
        <?php if (!empty($successMessage)) : ?>
            <div class="notification-message success"><?php echo $successMessage; ?></div>
        <?php elseif (!empty($errorMessage)) : ?>
            <div class="notification-message error"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <form id="add-attendance-form" class="add-course-form" method="POST" action="add-attendance-teacher.php">
            <label for="dateTime">(Date and Time):</label>
            <input type="datetime-local" id="dateTime" name="dateTime" required>
            
            <label for="classid">Class ID:</label>
            <select id="classid" name="classid" required>
                <option value="" disabled selected>Select Class</option>
                <?php
                foreach ($classes as $class) {
                    echo "<option value='" . htmlspecialchars($class['CLASS_ID']) . "'>" . htmlspecialchars($class['CLASS_NAME']) . "</option>";
                }
                ?>
            </select>

            <div class="form-buttons">
                <a href="manage-attendance.php" class="cancel-btn">Cancel</a>
                <button type="submit" id="add-course-btn" class="submit-btn">Add</button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript to handle datetime input -->
<script>
    // Get the current date and time in the required format
    const currentDateTime = new Date().toISOString().slice(0, 16);
    
    // Set the 'min' attribute of the datetime-local input field
    document.getElementById('dateTime').setAttribute('min', currentDateTime);
</script>
</body>
</html>

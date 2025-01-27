<?php
require 'db_config.php';

// Validate classID from GET parameter
if (!isset($_GET['CLASS_ID']) || empty($_GET['CLASS_ID'])) {
    die("Error: CLASS_ID is required.");
}

$classID = intval($_GET['CLASS_ID']); // Sanitize input

// SQL query to fetch class details
$sql = "SELECT * FROM CLASSES WHERE CLASS_ID = :classID";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ":classID", $classID);
oci_execute($stmt);

// Fetch class details for display
$classDetails = oci_fetch_assoc($stmt);

// Check if class exists
if (!$classDetails) {
    die("Class not found.");
}

// Update class details if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve updated values from the form
    $class_name = $_POST['class_name'];
    $class_duration = $_POST['class_duration'];
    $class_capacity = $_POST['class_capacity'];

    // Convert to 'YYYY-MM-DD HH24:MI:SS' format for Oracle
    $class_starttime = $_POST['class_starttime']; // The format will be 'Y-m-d\TH:i' from the input
    $class_endtime = $_POST['class_endtime']; // The format will be 'Y-m-d\TH:i' from the input

    // Convert the date to 'YYYY-MM-DD HH:MI:SS' format for Oracle
    $class_starttime = date('Y-m-d H:i:s', strtotime($class_starttime));
    $class_endtime = date('Y-m-d H:i:s', strtotime($class_endtime));

    $class_platform = $_POST['class_platform'];
    $subject_id = $_POST['subject_id'];
    $teacher_id = $_POST['teacher_id'];

    // SQL query to update class details
    $updateQuery = "
        UPDATE CLASSES 
        SET 
            CLASS_NAME = :class_name,
            CLASS_DURATION = :class_duration,
            CLASS_CAPACITY = :class_capacity,
            CLASS_STARTTIME = TO_DATE(:class_starttime, 'YYYY-MM-DD HH24:MI:SS'),
            CLASS_ENDTIME = TO_DATE(:class_endtime, 'YYYY-MM-DD HH24:MI:SS'),
            CLASS_PLATFORM = :class_platform,
            SUBJECT_ID = :subject_id,
            TEACHER_ID = :teacher_id
        WHERE CLASS_ID = :classID";

    // Prepare and execute the update statement
    $updateStmt = oci_parse($conn, $updateQuery);
    oci_bind_by_name($updateStmt, ":class_name", $class_name);
    oci_bind_by_name($updateStmt, ":class_duration", $class_duration);
    oci_bind_by_name($updateStmt, ":class_capacity", $class_capacity);
    oci_bind_by_name($updateStmt, ":class_starttime", $class_starttime);
    oci_bind_by_name($updateStmt, ":class_endtime", $class_endtime);
    oci_bind_by_name($updateStmt, ":class_platform", $class_platform);
    oci_bind_by_name($updateStmt, ":subject_id", $subject_id);
    oci_bind_by_name($updateStmt, ":teacher_id", $teacher_id);
    oci_bind_by_name($updateStmt, ":classID", $classID);

    if (oci_execute($updateStmt)) {
        echo "<script>
                alert('Class details updated successfully!');
                window.location.href = 'admin-class-register.php'; // Redirect after successful update
              </script>";
    } else {
        echo "<script>alert('Error updating class details.');</script>";
    }

    oci_free_statement($updateStmt);
}
oci_free_statement($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Class Details</title>
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
                <h2>Edit Class Details</h2>
            </div>
            <img src="images/student-icon2.jpg" alt="">
        </div>

        <div class="wrapper">
            <div class="content-wrapper">
                <form class="class-form" method="POST">
                    <div class="form-group">
                        <label for="class-name">Class Name:</label>
                        <input type="text" id="class-name" name="class_name" value="<?= htmlspecialchars($classDetails['CLASS_NAME']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="class-duration">Class Duration (hours):</label>
                        <input type="number" id="class-duration" name="class_duration" value="<?= htmlspecialchars($classDetails['CLASS_DURATION']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="class-capacity">Class Capacity:</label>
                        <input type="number" id="class-capacity" name="class_capacity" value="<?= htmlspecialchars($classDetails['CLASS_CAPACITY']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="class-starttime">Class Start Time:</label>
                        <input type="datetime-local" id="class-starttime" name="class_starttime" value="<?= date("Y-m-d\TH:i", strtotime($classDetails['CLASS_STARTTIME'])) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="class-endtime">Class End Time:</label>
                        <input type="datetime-local" id="class-endtime" name="class_endtime" value="<?= date("Y-m-d\TH:i", strtotime($classDetails['CLASS_ENDTIME'])) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="class-platform">Class Platform:</label>
                        <input type="text" id="class-platform" name="class_platform" value="<?= htmlspecialchars($classDetails['CLASS_PLATFORM']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="subject-id">Subject ID:</label>
                        <input type="number" id="subject-id" name="subject_id" value="<?= htmlspecialchars($classDetails['SUBJECT_ID']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="teacher-id">Teacher ID:</label>
                        <input type="number" id="teacher-id" name="teacher_id" value="<?= htmlspecialchars($classDetails['TEACHER_ID']) ?>" required>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn-submit">Update Class</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<?php
oci_close($conn);
?>

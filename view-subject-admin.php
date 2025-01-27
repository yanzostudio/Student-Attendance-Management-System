<?php
require 'db_config.php';

// Validate subjectID from GET parameter
if (!isset($_GET['SUBJECT_ID']) || empty($_GET['SUBJECT_ID'])) {
    die("Error: SUBJECT_ID is required.");
}

$subjectID = intval($_GET['SUBJECT_ID']); // Sanitize input

// SQL query to fetch subject details
$sql = "SELECT * FROM SUBJECTS WHERE SUBJECT_ID = :subjectID";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ":subjectID", $subjectID);
oci_execute($stmt);

// Fetch subject details for display
$subjectDetails = oci_fetch_assoc($stmt);

// Check if subject exists
if (!$subjectDetails) {
    die("Subject not found.");
}

// Update subject details if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve updated values from the form
    $subject_name = $_POST['subject_name'];
    $subject_description = $_POST['subject_description'];
    $subject_LOC = $_POST['subject_LOC'];
    $subject_difficulty = $_POST['subject_difficulty'];

    // SQL query to update subject details
    $updateQuery = "
        UPDATE SUBJECTS 
        SET 
            SUBJECT_NAME = :subject_name,
            SUBJECT_DESCRIPTION = :subject_description,
            SUBJECT_LISTOFCONTENT = :subject_LOC,
            SUBJECT_DIFFICULTY = :subject_difficulty
        WHERE SUBJECT_ID = :subjectID";

    // Prepare and execute the update statement
    $updateStmt = oci_parse($conn, $updateQuery);
    oci_bind_by_name($updateStmt, ":subject_name", $subject_name);
    oci_bind_by_name($updateStmt, ":subject_description", $subject_description);
    oci_bind_by_name($updateStmt, ":subject_LOC", $subject_LOC);
    oci_bind_by_name($updateStmt, ":subject_difficulty", $subject_difficulty);
    oci_bind_by_name($updateStmt, ":subjectID", $subjectID);

    if (oci_execute($updateStmt)) {
        echo "<script>
                alert('Subject details updated successfully!');
                window.location.href = 'admin-subject-register.php'; // Redirect after successful update
              </script>";
    } else {
        echo "<script>alert('Error updating subject details.');</script>";
    }

    oci_free_statement($updateStmt);
}
oci_free_statement($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Subject Details</title>
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
                <h2>Edit Subject Details</h2>
            </div>
            <img src="images/student-icon2.jpg" alt="">
        </div>

        <div class="wrapper">
            <div class="content-wrapper">
                <form class="subject-form" method="POST">
                    <div class="form-group">
                        <label for="subject-name">Subject Name:</label>
                        <input type="text" id="subject-name" name="subject_name" value="<?= htmlspecialchars($subjectDetails['SUBJECT_NAME']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="subject-description">Subject Description:</label>
                        <input type="text" id="subject-description" name="subject_description" value="<?= htmlspecialchars($subjectDetails['SUBJECT_DESCRIPTION']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="subject-LOC">Subject List of Content:</label>
                        <input type="text" id="subject-LOC" name="subject_LOC" value="<?= htmlspecialchars($subjectDetails['SUBJECT_LISTOFCONTENT']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="subject-difficulty">Subject Difficulty (1-10):</label>
                        <input type="number" id="subject-difficulty" name="subject_difficulty" value="<?= htmlspecialchars($subjectDetails['SUBJECT_DIFFICULTY']) ?>" required>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn-submit">Update Subject</button>
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

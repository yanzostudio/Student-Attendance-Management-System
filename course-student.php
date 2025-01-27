<?php
require 'db_config.php'; // Ensure proper connection
session_start();

$studentID = $_SESSION['studentID']; // Get the student ID from session

if ($studentID) {
    // Query to get the classes the student is already enrolled in
    $checkQuery = "
    SELECT c.Class_ID, c.Class_Name
    FROM classes c
    WHERE c.Class_ID IN (
        SELECT Class_ID FROM enrolls WHERE Student_ID = :studentID
    )
    ";
    $stmt = oci_parse($conn, $checkQuery);
    oci_bind_by_name($stmt, ":studentID", $studentID);
    oci_execute($stmt);
    $enrolledResult = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $enrolledResult[] = $row; // Store the result for enrolled classes
    }

    // Query to get the classes the student is NOT enrolled in
    $notEnrolledQuery = "
    SELECT c.Class_ID, c.Class_Name
    FROM classes c
    WHERE c.Class_ID NOT IN (
        SELECT Class_ID FROM enrolls WHERE Student_ID = :studentID
    )
    ";
    $stmt = oci_parse($conn, $notEnrolledQuery);
    oci_bind_by_name($stmt, ":studentID", $studentID);
    oci_execute($stmt);
    $notEnrolledResult = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $notEnrolledResult[] = $row; // Store the result for non-enrolled classes
    }

    oci_free_statement($stmt); // Free the statement
} else {
    $response['message'] = 'Invalid student ID.';
}

// Handle class enrollment action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $classID = $_POST['classID'];
    $stmts = oci_parse($conn, "INSERT INTO enrolls (Student_ID, Class_ID) VALUES (:studentID, :classID)");
    oci_bind_by_name($stmts, ":studentID", $studentID);
    oci_bind_by_name($stmts, ":classID", $classID);
    if (oci_execute($stmts)) {
        header('Location: course-student.php');
        exit;
    } else {
        echo "Error: " . oci_error($stmts)['message'];
    }
    oci_free_statement($stmts);
}
oci_close($conn); // Close the connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enroll Class</title>
    <link rel="stylesheet" href="css/course-student-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- sidebar -->
    <?php require 'sidebar-student.php'; ?>

    <div class="main-content">
        <div class="header-wrapper">
            <div class="header-title">
                <span>Student</span>
                <h2>Enroll Class</h2>
            </div>
            <img src="images/student-icon2.jpg" alt="" width="50px" height="50px">
        </div>
        
        <div class="container">
            <h3 class="main-title">Classes</h3>

            <!-- Classes Section (Enrolled and Not Enrolled) -->
            <div class="classes-section">
                <!-- Enrolled Classes Section -->
                <div class="enrolled-classes">
                    <h4>Enrolled Classes</h4>
                    <div class="class-list" id="enrolled">
                        <?php if (!empty($enrolledResult)): ?>
                            <?php foreach ($enrolledResult as $row): ?>
                                <div class="class-item">
                                    <h3><?php echo htmlspecialchars($row['CLASS_NAME']); ?></h3>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No classes enrolled yet.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Not Enrolled Classes Section -->
                <div class="not-enrolled-classes">
                    <h4>Classes Not Yet Enrolled</h4>
                    <div class="class-list" id="not-enrolled">
                        <?php if (!empty($notEnrolledResult)): ?>
                            <?php foreach ($notEnrolledResult as $row): ?>
                                <div class="class-item">
                                    <h3><?php echo htmlspecialchars($row['CLASS_NAME']); ?></h3>
                                </div>
                                <form action="course-student.php" method="POST">
                                    <input type="hidden" name="classID" value="<?= $row['CLASS_ID'] ?>">
                                    <input type="submit" value="Enroll" class="register-btn">
                                </form>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>All classes are enrolled.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

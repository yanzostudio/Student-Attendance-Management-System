<?php
require 'db_config.php'; // Ensure proper connection
session_start();

$studentID = $_SESSION['studentID']; // Get the student ID from session

if ($studentID) {
    // Query to get the classes the student is already enrolled in
    $checkQuery = "
    SELECT c.ClassID, c.ClassName
    FROM class c
    WHERE c.ClassID IN (
        SELECT ClassID FROM enroll WHERE StudentID = ?
    )
    ";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param('i', $studentID);
    $stmt->execute();
    $enrolledResult = $stmt->get_result(); // Store the result for enrolled classes

    // Query to get the classes the student is NOT enrolled in
    $notEnrolledQuery = "
    SELECT c.ClassID, c.ClassName
    FROM class c
    WHERE c.ClassID NOT IN (
        SELECT ClassID FROM enroll WHERE StudentID = ?
    )
    ";
    $stmt = $conn->prepare($notEnrolledQuery);
    $stmt->bind_param('i', $studentID);
    $stmt->execute();
    $notEnrolledResult = $stmt->get_result(); // Store the result for non-enrolled classes

    $stmt->close(); // Close the statement
} else {
    $response['message'] = 'Invalid student ID.';
}

// Handle class enrollment action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $classID = $_POST['classID'];
    $stmts = $conn->prepare("INSERT INTO enroll (StudentID, ClassID) VALUES (?, ?)");
    $stmts->bind_param("ii", $studentID, $classID);
    if ($stmts->execute()) {
        header('Location: course-student.php');
        exit;
    } else {
        echo "Error: " . $stmts->error;
    }
    $conn->close();
}
$conn->close();
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
            <img src="images/student-icon2.jpg" alt="">
        </div>
        
        <div class="container">
            <h3 class="main-title">Classes</h3>

            <!-- Classes Section (Enrolled and Not Enrolled) -->
            <div class="classes-section">
                <!-- Enrolled Classes Section -->
                <div class="enrolled-classes">
                    <h4>Enrolled Classes</h4>
                    <div class="class-list" id="enrolled">
                        <?php if ($enrolledResult && $enrolledResult->num_rows > 0): ?>
                            <?php while ($row = $enrolledResult->fetch_assoc()): ?>
                                <div class="class-item">
                                    <h3><?php echo htmlspecialchars($row['ClassName']); ?></h3>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>No classes enrolled yet.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Not Enrolled Classes Section -->
                <div class="not-enrolled-classes">
                    <h4>Classes Not Yet Enrolled</h4>
                    <div class="class-list" id="not-enrolled">
                        <?php if ($notEnrolledResult && $notEnrolledResult->num_rows > 0): ?>
                            <?php while ($row = $notEnrolledResult->fetch_assoc()): ?>
                                <div class="class-item">
                                    <h3><?php echo htmlspecialchars($row['ClassName']); ?></h3>
                                </div>
                                <form action="course-student.php" method="POST">
                                    <input type="hidden" name="classID" value="<?= $row['ClassID'] ?>">
                                    <input type="submit" value="Enroll" class="register-btn">
                                </form>
                            <?php endwhile; ?>
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

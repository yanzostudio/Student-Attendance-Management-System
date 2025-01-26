<?php
$servername = "localhost";
$username = "root"; // Change if necessary
$password = ""; // Change if necessary
$dbname = "sams_db"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate classID from GET parameter
if (!isset($_GET['classID']) || empty($_GET['classID'])) {
    die("Error: Class ID is required.");
}

$classID = intval($_GET['classID']); // Sanitize input

// SQL query to fetch attendance data
$sql = "
    SELECT a.StudentID, s.username, a.AttendanceStatus
    FROM attendance a
    JOIN student s ON a.StudentID = s.StudentID
    JOIN class c ON a.ClassID = c.ClassID
    WHERE c.ClassID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $classID);
$stmt->execute();
$result = $stmt->get_result();

// Fetch class name for display
$classQuery = "SELECT ClassName FROM class WHERE ClassID = ?";
$classStmt = $conn->prepare($classQuery);
$classStmt->bind_param("i", $classID);
$classStmt->execute();
$classResult = $classStmt->get_result();
$className = $classResult->fetch_assoc()['ClassName'] ?? 'Unknown Class';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate Report - <?= htmlspecialchars($className) ?></title>
    <link rel="stylesheet" href="css/list-of-class-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- sidebar -->
    <?php require 'sidebar-admin.php'; ?>

    <div class="main-content">
        <div class="header-wrapper">
            <div class="header-title">
                <span>Admin</span>
                <h2><?= htmlspecialchars($className) ?></h2>
            </div>
            <img src="images/student-icon2.jpg" alt="">
        </div>

        <div class="tabular-wrapper">
            <h3 class="main-title">Attendance Report - <?= htmlspecialchars($className) ?></h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Bil.</th>
                            <th>Name</th>
                            <th>Student ID</th>
                            <th>Attendance (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($students)): ?>
                            <?php foreach ($students as $index => $student): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($student['Name']) ?></td>
                                    <td><?= htmlspecialchars($student['StudentID']) ?></td>
                                    <td><?= number_format($student['AttendancePercentage'], 2) ?>%</td>
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
                <a href="list-of-class.php" class="btn return-btn">Return</a>
                <button onclick="window.print()" class="btn print-btn">Print</button>
            </div>
        </div>
    </div>
</body>
</html>

<?php 
require 'db_config.php'; // Sambungkan ke database
session_start();

// Pastikan pengguna telah log masuk
if (!isset($_SESSION['studentID'])) {
    die("Access denied. Please log in.");
}

$studentID = $_SESSION['studentID'];

// Query untuk dapatkan rekod kehadiran pelajar
$sql = "SELECT a.dateTime, a.AttendanceStatus, s.StudentID, c.ClassName, co.CourseName
FROM attendance a
JOIN student s ON a.StudentID = s.StudentID
JOIN class c ON a.ClassID = c.ClassID
JOIN course co ON c.CourseID = co.courseID
WHERE s.StudentID = ?
ORDER BY a.dateTime DESC;";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $studentID); // Gunakan student ID dari sesi
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance</title>
    <link rel="stylesheet" href="css/attendance-student-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo"></div>
        <ul class="menu">
            <li><a href="dashboard-student.php"><i class="fa-solid fa-house-user"></i> <span>Homepage</span></a></li>
            <li class="active"><a href="attendance-student.php"><i class="fa-regular fa-calendar-days"></i> <span>Attendance</span></a></li>
            <li><a href="classes-student.php"><i class="fa-solid fa-users"></i> <span>Classes</span></a></li>
            <li><a href="course-student.php"><i class="fa-solid fa-book"></i> <span>Course</span></a></li>
            <li class="logout"><a href="login.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header-wrapper">
            <div class="header-title">
                <span>Student</span>
                <h2>Attendance</h2>
            </div>
            <img src="images/student-icon2.jpg" alt="Student">
        </div>

        <div class="tabular-wrapper">
            <h3 class="main-title">Attendance</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Class</th>
                            <th>Course</th>
                            <th>Date</th>
                            <th>Attendance Status</th>
                            <th>Take Attendance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['StudentID']) ?></td>
                                    <td><?= htmlspecialchars($row['ClassName']) ?></td>
                                    <td><?= htmlspecialchars($row['CourseName']) ?></td>
                                    <td><?= date('d M Y', strtotime($row['dateTime'])) ?></td>
                                    <td><?= $row['AttendanceStatus'] == 1 ? 'Present' : 'Absent' ?></td>
                                    <td>
                                    <button class="facial-scan" onclick="showFacialScanPopup(); startFacialRecognition(<?= htmlspecialchars($row['StudentID']) ?>)">
                                         <i class="fa-solid fa-camera"></i> Facial Scan
                                    </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No attendance records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div id="facial-scan-popup" style="display:none;">
            <video id="video" autoplay></video>
        </div>
    </div>
   
</body>
</html>
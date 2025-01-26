<?php 
require 'db_config.php'; // Database connection
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['studentID'])) {
    die("Access denied. Please log in.");
}

$studentID = $_SESSION['studentID'];

// Query to fetch attendance records
$sql = "SELECT a.AttendanceID, a.dateTime, a.AttendanceStatus, s.StudentID, c.ClassName, co.CourseName
FROM attendance a
JOIN student s ON a.StudentID = s.StudentID
JOIN class c ON a.ClassID = c.ClassID
JOIN course co ON c.CourseID = co.courseID
WHERE s.StudentID = ?
ORDER BY a.dateTime DESC;";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $studentID); // Use student ID from session
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
                                <tr id="row-<?= htmlspecialchars($row['StudentID']) ?>">
                                    <td><?= htmlspecialchars($row['StudentID']) ?></td>
                                    <td><?= htmlspecialchars($row['ClassName']) ?></td>
                                    <td><?= htmlspecialchars($row['CourseName']) ?></td>
                                    <td><?= date('d M Y', strtotime($row['dateTime'])) ?></td>
                                    <td class="attendance-status"><?= $row['AttendanceStatus'] == 1 ? 'Present' : 'Absent' ?></td>
                                    <td>
    <?php if ($row['AttendanceStatus'] == 0): ?>
        <button class="facial-scan" onclick="showFacialScanPopup('<?php echo htmlspecialchars($row['StudentID']); ?>', '<?php echo htmlspecialchars($row['AttendanceID']); ?>')">
            <i class="fa-solid fa-camera"></i> Facial Scan
        </button>
    <?php else: ?>
        <span class="success-icon">
            <i class="fa-solid fa-check-circle"></i> Attended
        </span>
    <?php endif; ?>
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
    </div>

    <div id="facial-scan-popup" class="popup-container">
        <div class="popup-content">
            <video id="video" autoplay></video>
            <p>Scanning your face... Please wait.</p>
        </div>
    </div>

    <script>
        const video = document.getElementById("video");

        function showFacialScanPopup(studentID, attendanceID) {
    const popup = document.getElementById("facial-scan-popup");
    popup.style.display = "flex";

    // Start camera
    navigator.mediaDevices.getUserMedia({ video: true })
        .then((stream) => {
            video.srcObject = stream;
            startMockFacialRecognition(studentID, attendanceID, stream);
        })
        .catch((err) => {
            console.error("Error accessing the camera:", err);
            alert("Unable to access the camera.");
        });
}

function startMockFacialRecognition(studentID, attendanceID, stream) {
    setTimeout(() => {
        // Stop the camera feed
        stream.getTracks().forEach((track) => track.stop());
        document.getElementById("facial-scan-popup").style.display = "none";

        // Simulate marking attendance
        fetch("mock-update-attendance.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ studentID: studentID, attendanceID: attendanceID }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.status === "success") {
                    console.log("Received data:", data);
                    console.log("Looking for row:", `row-${attendanceID}`);

                    // Attempt to find the row
                    const row = document.getElementById(`row-${attendanceID}`) || document.getElementById(`row-${studentID}`);
                    if (row) {
                        row.querySelector(".attendance-status").textContent = "Present";
                        row.querySelector("td:last-child").innerHTML = `
                            <span class="success-icon">
                                <i class="fa-solid fa-check-circle"></i> Attended
                            </span>
                        `;

                        // Show alert and refresh the page after alert
                        alert("Facial scan successful. Attendance marked!");
                        location.reload(); // Refresh the page immediately after the alert is dismissed
                    } else {
                        console.error("Row not found for either attendanceID or studentID.");
                    }
                } else {
                    alert("Failed to update attendance: " + data.message);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert("Error updating attendance.");
            });
    }, 2000); // Simulated 2-second scan
}






    </script>
</body>
</html>

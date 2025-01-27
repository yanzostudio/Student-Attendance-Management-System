<?php  
require 'db_config.php'; // Database connection
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['studentID'])) {
    die("Access denied. Please log in.");
}

$studentID = $_SESSION['studentID'];

// Oracle Database connection
$conn = oci_connect($username, $password, $servername);
if (!$conn) {
    $e = oci_error();
    die("Oracle Connection failed: " . $e['message']);
}

// Query to fetch attendance records
$sql = "SELECT a.CLASS_ID, a.ATTENDANCE_TIME, a.ATTENDED, s.STUDENT_ID, c.CLASS_NAME, c.CLASS_STARTTIME, c.CLASS_ENDTIME
        FROM ATTENDANCE a
        JOIN STUDENTS s ON a.STUDENT_ID = s.STUDENT_ID
        JOIN CLASSES c ON a.CLASS_ID = c.CLASS_ID
        WHERE s.STUDENT_ID = :studentID
        ORDER BY a.ATTENDANCE_TIME DESC";

// Prepare and execute query using Oracle functions
$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ":studentID", $studentID);

oci_execute($stid);

// Close connection
oci_close($conn);
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
<?php require 'sidebar-student.php'; ?>

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
                            <th>Class Start Time</th>
                            <th>Class End Time</th>
                            <th>Attendance Status</th>
                            <th>Mark Attendance</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
if ($row = oci_fetch_assoc($stid)): 
    while ($row): 
?>
        <tr id="row-<?= htmlspecialchars($row['STUDENT_ID']) ?>">
            <td><?= htmlspecialchars($row['STUDENT_ID']) ?></td>
            <td><?= htmlspecialchars($row['CLASS_NAME']) ?></td>
            <td><?= date('d M Y H:i', strtotime($row['CLASS_STARTTIME'])) ?></td>
            <td><?= date('d M Y H:i', strtotime($row['CLASS_ENDTIME'])) ?></td>
            <td class="attendance-status"><?= $row['ATTENDED'] == 'Y' ? 'Present' : 'Absent' ?></td>
            <td>
                <?php if ($row['ATTENDED'] == 'N'): ?>
                    <button class="mark-attendance" onclick="markAttendance(<?= htmlspecialchars($row['STUDENT_ID']) ?>, <?= htmlspecialchars($row['CLASS_ID']) ?>)">
                        <i class="fa-solid fa-check-circle"></i> Mark Attendance
                    </button>
                <?php else: ?>
                    <span class="success-icon">
                        <i class="fa-solid fa-check-circle"></i> Attended
                    </span>
                <?php endif; ?>
            </td>
        </tr>
<?php 
        $row = oci_fetch_assoc($stid); // Fetch the next row
    endwhile;
else: 
?>
    <tr>
        <td colspan="6">No attendance records found.</td>
    </tr>
<?php endif; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Attendance Confirmation Popup -->
    <div id="attendance-popup" class="popup-container">
        <div class="popup-content">
            <p>Attendance successfully marked! Thank you.</p>
            <button onclick="closePopup()">Close</button>
        </div>
    </div>

    <script>
        function markAttendance(studentID, classID) {
    // Send request to update the attendance in the database
    fetch("update-attendance.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ studentID: studentID, classID: classID }),
    })
    .then((response) => response.json())
    .then((data) => {
        if (data.status === "success") {
            // Mark attendance as present on the page
            const row = document.getElementById(`row-${studentID}`);
            if (row) {
                row.querySelector(".attendance-status").textContent = "Present";
                row.querySelector("td:last-child").innerHTML = `
                    <span class="success-icon">
                        <i class="fa-solid fa-check-circle"></i> Attended
                    </span>
                `;
            }

            // Show popup message and refresh the page
            showPopup();
        } else {
            alert("Failed to mark attendance: " + data.message);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
        alert("Error updating attendance.");
    });
}


        function showPopup() {
            const popup = document.getElementById("attendance-popup");
            popup.style.display = "flex";
        }

        function closePopup() {
            const popup = document.getElementById("attendance-popup");
            popup.style.display = "none";
            location.reload(); // Refresh the page
        }
    </script>
</body>
</html>

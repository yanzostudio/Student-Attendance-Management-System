<?php 
    require 'db_config.php';
    session_start();

    if (empty($_SESSION['teacherID'])) {
        header("Location: login.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Update attendance for students
        $dateTime = $_POST['dateTime'];
        $classID = $_POST['classid'];
        $attendance = $_POST['attendance'];

        // Delete existing attendance for this date and class
        $deleteQuery = "DELETE FROM attendance WHERE dateTime = ? AND ClassID = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("si", $dateTime, $classID);
        $stmt->execute();

        // Insert updated attendance records
        $insertQuery = "INSERT INTO attendance (dateTime, StudentID, ClassID) VALUES (?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);

        foreach ($attendance as $studentID => $status) {
            if ($status === 'present') {
                $insertStmt->bind_param("sii", $dateTime, $studentID, $classID);
                $insertStmt->execute();
            }
        }

        echo "<script>alert('Attendance updated successfully!');</script>";
    }

    // Fetch classes for the teacher
    $classQuery = "SELECT * FROM class WHERE TeacherID = ?";
    $classStmt = $conn->prepare($classQuery);
    $classStmt->bind_param("i", $_SESSION['teacherID']);
    $classStmt->execute();
    $classes = $classStmt->get_result();

    // Fetch students and attendance if class is selected
    $students = [];
    if (!empty($_GET['classid']) && !empty($_GET['dateTime'])) {
        $classID = $_GET['classid'];
        $dateTime = $_GET['dateTime'];

        $studentQuery = "
            SELECT e.StudentID, s.name, 
                   IF(a.StudentID IS NULL, 'absent', 'present') AS AttendanceStatus
            FROM enroll e
            JOIN student s ON e.StudentID = s.StudentID
            LEFT JOIN attendance a ON e.StudentID = a.StudentID AND a.dateTime = ? AND a.ClassID = ?
            WHERE e.ClassID = ?";
        $stmt = $conn->prepare($studentQuery);
        $stmt->bind_param("sii", $dateTime, $classID, $classID);
        $stmt->execute();
        $students = $stmt->get_result();
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
			<a href="add-attendance-teacher.php">Add Attendance</a>
        </div>

        <form method="GET" action="manage-attendance.php">
            <label for="classid">Class:</label>
            <select id="classid" name="classid" required>
                <option value="" disabled selected>Select Class</option>
                <?php while ($class = $classes->fetch_assoc()): ?>
                    <option value="<?= $class['ClassID'] ?>" <?= (isset($_GET['classid']) && $_GET['classid'] == $class['ClassID']) ? 'selected' : '' ?>>
                        <?= $class['ClassName'] ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="dateTime">Date:</label>
            <input type="date" id="dateTime" name="dateTime" value="<?= $_GET['dateTime'] ?? '' ?>" required>

            <button type="submit">Load Attendance</button>
        </form>

        <?php if (!empty($students)): ?>
            <form method="POST" action="manage-attendance.php">
                <input type="hidden" name="classid" value="<?= htmlspecialchars($_GET['classid']) ?>">
                <input type="hidden" name="dateTime" value="<?= htmlspecialchars($_GET['dateTime']) ?>">

                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Attendance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($student = $students->fetch_assoc()): ?>
                            <tr>
                                <td><?= $student['StudentID'] ?></td>
                                <td><?= $student['name'] ?></td>
                                <td>
                                    <input type="checkbox" name="attendance[<?= $student['StudentID'] ?>]" value="present" <?= ($student['AttendanceStatus'] === 'present') ? 'checked' : '' ?>>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <button type="submit">Update Attendance</button>
            </form>
        <?php endif; ?>
    </div>
    
</body>
</html>

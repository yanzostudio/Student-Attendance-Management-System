<?php
    require 'db_config.php';
    session_start();

    // Check if the teacher is logged in
    if (!isset($_SESSION['teacherID'])) {
        header("Location: login.php");
        exit();
    }

    $teacherID = $_SESSION['teacherID'];

    // Fetch teacher details
    $query = "SELECT username, Email FROM teacher WHERE teacherID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $teacherID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $teacher = $result->fetch_assoc();
        $teacherName = $teacher['username'];
        $teacherEmail = $teacher['Email'];
    } else {
        echo "Error fetching teacher details.";
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="css/dashboard-teacher-style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
<?php require 'sidebar-teacher.php'; ?>

<div class="main--content">
    <div class="header--wrapper">
        <div class="header--title">
            <span>Teacher</span>
            <h2>Dashboard</h2>
        </div>
        <div class="user--info">
            <img src="images/teacher.png" alt="Teacher Profile">
        </div>
    </div>

    <!-- Menu Section -->
    <div class="card--container">
        <h3 class="main--title">Menu</h3>
        <div class="card--wrapper">
            <!-- Profile Card -->
            <div class="payment--card light-blue">
                <div class="card--header">
                    <div class="amount">
                        <span class="title">Profile</span>
                        <span class="title">Name: <?= htmlspecialchars($teacherName); ?></span>
                        <span class="title">Teacher ID: <?= htmlspecialchars($teacherID); ?></span>
                        <span class="title">Email: <?= htmlspecialchars($teacherEmail); ?></span>
                    </div>
                    <i class="fas fa-user icon dark-blue"></i>
                </div>
            </div>

            <!-- Schedule Card -->
            <div class="payment--card light-pink">
                    <div class="card--header">
                        <div class="amount">
                            <span class="title">Schedule</span>
                        </div>
                        <i class="fas fa-briefcase icon purple"></i>
                    </div>
                </a>
            </div>

            <!-- Attendance Card -->
            <div class="payment--card light-red">
                    <div class="card--header">
                        <div class="amount">
                            <span class="title">Attendance</span>
                        </div>
                        <i class="fas fa-list icon red"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- List of Classes Section -->
    <div class="tabular--wrapper">
        <h3 class="main--title">List of Classes</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Class ID</th>
                        <th>Class Name</th>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Schedule</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $query = "SELECT c.ClassID, c.ClassName, cr.CourseCode, cr.CourseName, 
                                     IFNULL(s.TimeSlot, 'TBA') AS TimeSlot
                              FROM class c
                              JOIN course cr ON c.CourseID = cr.CourseID
                              LEFT JOIN schedule s ON c.ClassID = s.ClassID
                              WHERE c.TeacherID = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $teacherID);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    $bil = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$bil}</td>
                            <td>{$row['ClassID']}</td>
                            <td>{$row['ClassName']}</td>
                            <td>{$row['CourseCode']}</td>
                            <td>{$row['CourseName']}</td>
                            <td>{$row['TimeSlot']}</td>
                        </tr>";
                        $bil++;
                    }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>

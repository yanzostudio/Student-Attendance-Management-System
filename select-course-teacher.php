<?php
        //session_start();
        // Include database connection file
        //require 'session.php';
        require 'db_config.php'; // Ensure proper connection
        session_start();
        // Fetch courses assigned to the teacher
        $teacherID = $_SESSION['teacherID']; // Assuming you store teacher ID in session
        $query = "SELECT CourseCode FROM course WHERE TeacherID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $teacherID);
        $stmt->execute();
        $result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Course</title>
    <link rel="stylesheet" href="css/select-course-teacher.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
<?php require 'sidebar-teacher.php'; ?>

    <div class="main-content">
        <div class="header-wrapper">
            <div class="header-title">
                <span>Teacher</span>
                <h2>Course</h2>
            </div>
            <img src="images/teacher.png">
        </div>

        <div class="container">
            <h3 class="main-title">New Schedule</h3>
            <div class="course-buttons">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <a href="add-schedule-teacher.php?course=<?= $row['CourseCode']; ?>" class="course-btn"><?= $row['CourseCode']; ?></a>
                <?php endwhile; ?>
            </div>
            <div class="add-course-container">
			<a href="add-course-teacher.php" class="add-course-btn">Add New Course</a>
        </div>
    </div>
</body>
</html>
<?php
        $stmt->close();
        $conn->close();
?>
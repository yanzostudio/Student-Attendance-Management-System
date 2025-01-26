<?php 
    require 'db_config.php'; 
    session_start();
    
    if ($_SESSION['teacherID'] == null) {
        header("Location: login.php");
        exit();
    }

    $successMessage = ''; // Variable to hold success message
    $errorMessage = ''; // Variable to hold error message

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $ClassName = $_POST['ClassName'];
        $teacherID = $_SESSION['teacherID'];
        $CourseID = $_POST['CourseID'];

        if (!empty($ClassName) && !empty($CourseID)) {
            // Insert class into database
            $sql = "INSERT INTO class (ClassName, TeacherID, CourseID) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $ClassName, $teacherID, $CourseID); 
    
            if ($stmt->execute()) {
                // $ClassID = $conn->insert_id; // Get the last inserted ClassID

                // // Optionally insert a default schedule for the new class
                // $scheduleSql = "INSERT INTO schedule (ClassID, TimeSlot) VALUES (?, ?)";
                // $scheduleStmt = $conn->prepare($scheduleSql);
                // $timeSlot = "TBA"; // Default placeholder for the schedule
                // $scheduleStmt->bind_param("is", $ClassID, $timeSlot);
                // $scheduleStmt->execute();
                // $scheduleStmt->close();

                // $successMessage = 'Class added successfully!';
            } else {
                $errorMessage = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $errorMessage = "Please fill in all required fields.";
        }
    }

    $courseQuery = "SELECT CourseID, CourseName FROM course";
    $courseResult = $conn->query($courseQuery);
    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course</title>
    <link rel="stylesheet" href="css/add-course-teacher.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .success-message {
            display: <?= $successMessage ? 'block' : 'none'; ?>;
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
            text-align: center;
            font-size: 1.1rem;
        }
        .error-message {
            display: <?= $errorMessage ? 'block' : 'none'; ?>;
            background-color: #f44336;
            color: white;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
            text-align: center;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
<?php require 'sidebar-teacher.php'; ?>

<div class="main-content">
    <div class="header-wrapper">
        <div class="header-title">
            <span>Teacher</span>
            <h2>Add Class</h2>
        </div>
        <img src="images/teacher.png">
    </div>

    <div class="container">
        <h3 class="main-title">Add a New Class</h3>

        <!-- Success or Error message -->
        <div class="success-message"><?= $successMessage; ?></div>
        <div class="error-message"><?= $errorMessage; ?></div>

        <!-- Form to add course -->
        <form id="add-course-form" class="add-course-form" method="POST" action="add-class-teacher.php">
            <label for="ClassName">Class Name:</label>
            <input type="text" id="ClassName" name="ClassName" placeholder="Enter class name" required>

            <label for="CourseID">Course Code:</label>
            <select id="CourseID" name="CourseID" required>
                <option value="">Select a Course</option>
                <?php 
                    if ($courseResult->num_rows > 0) {
                        while ($row = $courseResult->fetch_assoc()) {
                            echo "<option value='" . $row['CourseID'] . "'>" . $row['CourseName'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>No courses available</option>";
                    }
                ?>
            </select>

            <div class="form-buttons">
                <a href="classes-teacher.php" class="cancel-btn">Cancel</a>
                <button type="submit" id="add-course-btn" class="submit-btn">Add</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>

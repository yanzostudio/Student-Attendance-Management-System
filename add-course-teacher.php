<?php 
    require 'db_config.php'; // Ensure proper connection
    session_start();
    
    // Check if the user is logged in
    if ($_SESSION['teacherID'] == null) {
        header("Location: login.php");
        exit();
    }

    $successMessage = ''; // Variable to store success message
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get form data
        $course_name = $_POST['course-name'];
        $course_desc = $_POST['course-desc'];
        $course_code = $_POST['course-code'];
        $teacher_name = $_POST['course-instructor'];
    
        // Assuming teacher's ID is stored in the session
        $teacherID = $_SESSION['teacherID'];
    
        // Insert course into database
        $sql = "INSERT INTO course (CourseCode, CourseName, TeacherID) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $course_code, $course_name, $teacherID); // s for string, i for integer
    
        if ($stmt->execute()) {
            // Success, set the success message
            $successMessage = 'Course added successfully!';
        } else {
            echo "Error: " . $stmt->error;
        }
    
        $stmt->close();
        $conn->close();
    }
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
        /* Success message styling */
        .success-message {
            display: <?php echo $successMessage ? 'block' : 'none'; ?>;
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
            text-align: center;
            font-size: 1.1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    
<?php require 'sidebar-teacher.php'; ?>

    <div class="main-content">
        <div class="header-wrapper">
            <div class="header-title">
                <span>Teacher</span>
                <h2>Add Course</h2>
            </div>
            <img src="images/teacher.png">
        </div>

        <div class="container">
            <h3 class="main-title">Add a New Course</h3>

            <!-- Success message -->
            <div id="success-message" class="success-message">
                <?php echo $successMessage; ?>
            </div>

            <!-- Form to add course -->
            <form id="add-course-form" class="add-course-form" method="POST" action="add-course-teacher.php">
                <label for="course-name">Course Name:</label>
                <input type="text" id="course-name" name="course-name" placeholder="Enter course name" required>

                <label for="course-desc">Course Description:</label>
                <textarea id="course-desc" name="course-desc" placeholder="Enter course description" rows="4" required></textarea>

                <label for="course-code">Course Code:</label>
                <input type="text" id="course-code" name="course-code" placeholder="Enter course code">

                <label for="course-instructor">Teacher Name:</label>
                <input type="text" id="course-instructor" name="course-instructor" placeholder="Enter teacher name" required>

                <div class="form-buttons">
                    <a href="select-course-teacher.php" class="cancel-btn">Cancel</a>
                    <button type="submit" id="add-course-btn" class="submit-btn">Add</button>
                </div>
            </form>

        </div>
    </div>

</body>
</html>

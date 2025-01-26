<?php 
    require 'db_config.php'; // Ensure proper connection
    session_start();

    // Check if the user is logged in
    if ($_SESSION['teacherID'] == null) {
        header("Location: login.php");
        exit();
    }

    $query = "SELECT * FROM class WHERE TeacherID = " . $_SESSION['teacherID'] . ";";
    $result = mysqli_query($conn, $query);

    $successMessage = ''; // Variable to store success message
    $errorMessage = '';   // Variable to store error message

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get form data
        $ClassID = $_POST['classid'];
        $dateTime = $_POST['dateTime'];

        // Get all students enrolled in the selected class
        $enrollQuery = "SELECT StudentID FROM enroll WHERE ClassID = ?";
        $stmt = $conn->prepare($enrollQuery);
        $stmt->bind_param("i", $ClassID); // Assuming ClassID is an integer
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if there are students enrolled in this class
        if ($result->num_rows > 0) {
            // Loop through each student and insert them into the attendance table
            $attendanceQuery = "INSERT INTO attendance (dateTime, StudentID, ClassID) VALUES (?, ?, ?)";
            $attendanceStmt = $conn->prepare($attendanceQuery);
            $attendanceStmt->bind_param("sii", $dateTime, $studentID, $ClassID);

            // Loop through the result and insert each student into the attendance table
            while ($row = $result->fetch_assoc()) {
                $studentID = $row['StudentID'];
                if (!$attendanceStmt->execute()) {
                    $errorMessage = "Error inserting attendance for student ID " . $studentID . ": " . $attendanceStmt->error;
                }
            }

            if (!$errorMessage) {
                // Set success message if no errors occurred
                $successMessage = 'Attendance added successfully!';
            }

            // Close statements
            $attendanceStmt->close();
            $stmt->close();
        } else {
            $errorMessage = "No students enrolled in this class.";
        }

        // Close the connection
        $conn->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Attendance</title>
    <link rel="stylesheet" href="css/add-course-teacher.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
<?php require 'sidebar-teacher.php'; ?>

    <div class="main-content">
        <div class="header-wrapper">
            <div class="header-title">
                <span>Teacher</span>
                <h2>Add Attendance</h2>
            </div>
            <img src="images/teacher.png">
        </div>

        <div class="container">
            <h3 class="main-title">Add a New Attendance</h3>

            <!-- Notification Message -->
            <?php if (!empty($successMessage)) : ?>
                <div class="notification-message success"><?php echo $successMessage; ?></div>
            <?php elseif (!empty($errorMessage)) : ?>
                <div class="notification-message error"><?php echo $errorMessage; ?></div>
            <?php endif; ?>

            <form id="add-attendance-form" class="add-course-form" method="POST" action="add-attendance-teacher.php">
                <label for="dateTime">(Date and Time):</label>
                <input type="datetime-local" id="dateTime" name="dateTime" required>
                
                <label for="classid">Class ID:</label>
                <select id="classid" name="classid" required>
                    <option value="" disabled selected>Select Class</option>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='" . $row['ClassID'] . "'>" . $row['ClassName'] . "</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No classes available</option>";
                    }
                    ?>
                </select>

                <div class="form-buttons">
                    <a href="dashboard-teacher.php" class="cancel-btn">Cancel</a>
                    <button type="submit" id="add-course-btn" class="submit-btn">Add</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript to handle datetime input -->
    <script>
        // Get the current date and time in the required format
        const currentDateTime = new Date().toISOString().slice(0, 16);
        
        // Set the 'min' attribute of the datetime-local input field
        document.getElementById('dateTime').setAttribute('min', currentDateTime);
    </script>
</body>
</html>

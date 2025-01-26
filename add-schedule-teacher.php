<?php 
        // Start session
        //session_start();

         //require 'session.php';
         require 'db_config.php'; // Ensure proper connection
        session_start();
        // Check if the user is logged in
        if (($_SESSION['teacherID'])==null) {
            header("Location: login.php");
            exit();
        }

        // Handle form submission for adding schedule
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get form data
        $time = $_POST['time'];
        $location = $_POST['location'];
        $class_name = $_POST['class-name'];
        $teacherID = $_SESSION['teacherID'];

        // Get class ID from class name
        $stmt = $conn->prepare("SELECT ClassID FROM class WHERE ClassName = ?");
        $stmt->bind_param("s", $class_name);
        $stmt->execute();
        $stmt->bind_result($classID);
        $stmt->fetch();
        $stmt->close();

        // Insert the schedule data into the database
        $stmt = $conn->prepare("INSERT INTO schedule (TimeSlot, Location, ClassID, TeacherID) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $time, $location, $classID, $teacherID);
        
        if ($stmt->execute()) {
            // Redirect or display a success message
            echo "<script>alert('Schedule added successfully!'); window.location.href = 'select-course-teacher.php';</script>";
        } else {
            echo "<script>alert('Error adding schedule. Please try again later.');</script>";
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
    <title>Add Schedule</title>
    <link rel="stylesheet" href="css/add-schedule-teacher.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Success message styling */
        .success-message {
            display: none;
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
            text-align: center;
            font-size: 1.1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Return Button Styling */
        .return-btn {
            display: inline-block;
            background-color: #ccc;
            color: #333;
            padding: 0.8rem 1.5rem;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 1rem;
            transition: background-color 0.3s ease;
        }

        .return-btn:hover {
            background-color: #bbb;
        }

        /* Add Button Styling */
        .add-btn {
            background-color: #4CAF50;
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-left: 1rem;
        }

        .add-btn:hover {
            background-color: #45a049;
        }

        .button-container {
            display: flex;
            justify-content: flex-start;
            gap: 1rem;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php require 'sidebar-teacher.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header-wrapper">
            <div class="header-title">
                <span>Teacher</span>
                <h2>Add Schedule</h2>
            </div>
            <img src="images/teacher.png">
        </div>

        <div class="form-container">
            <h3 class="main-title">Add Schedule</h3>
            <!-- Success message -->
            <div id="success-message" class="success-message">Schedule added successfully!</div>
            
            <!-- Form -->
            <form class="schedule-form" method="POST" action="add-schedule-teacher.php">
                <label for="time">Time:</label>
                <select id="time" name="time" required>
                    <option value="" disabled selected>Select time slot</option>
                    <option value="08:00-10:00">08:00-10:00</option>
                    <option value="10:00-12:00">10:00-12:00</option>
                    <option value="12:00-13:00">12:00-13:00</option>
                    <option value="13:00-14:00">13:00-14:00</option>
                    <option value="14:00-16:00">14:00-16:00</option>
                    <option value="16:00-18:00">16:00-18:00</option>
                </select>
                
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" placeholder="Enter location" required>
                
                <label for="class-name">Class Name:</label>
                <input type="text" id="class-name" name="class-name" placeholder="Enter class name" required>

                <div class="button-container">
                    <a href="select-course-teacher.php" class="return-btn">Return</a>
                    <button type="submit" class="add-btn">Add</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.getElementById('schedule-form').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the form from submitting
            
            // Show the success message
            const successMessage = document.getElementById('success-message');
            successMessage.style.display = 'block';

            // Optional: Hide the success message after 10 seconds
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 10000); // 10 seconds
        });
    </script>
</body>
</html>

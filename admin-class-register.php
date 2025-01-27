<?php 
    require 'db_config.php'; // Ensure proper connection

    $conn->close();
    

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Admin</title>
        <link rel="stylesheet" href="css/admin-course-styles.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>

    <body>
<!-- Sidebar -->
<?php require 'sidebar-admin.php'; ?>

        <div class="main-content">
            <div class="header-wrapper">
                <div class="header-title">
                    <span>Admin</span>
                    <h2>Register New Course</h2>
                </div>
                <img src="images/manager.png">
            </div>
    
            <div class="wrapper">
                <div class="content-wrapper">
                    <div class="table-container">
                        <table id="course-table" border="1">
                            <thead>
                                <tr>
                                    <th>Course Name</th>
                                    <th>Course Code</th>
                                    <th>Teacher's Name</th>
                                    <th>Staff ID</th>
                                    <th>Class</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Rows will be dynamically added here -->
                            </tbody>
                        </table>
                    </div>
    
                    <form class="course-form" id="course-form">
                        <div class="form-group">
                            <label for="course-name">Course Name:</label>
                            <input type="text" id="course-name" name="course_name" placeholder="Enter course name" required>
                        </div>
    
                        <div class="form-group">
                            <label for="course-code">Course Code:</label>
                            <input type="text" id="course-code" name="course_code" placeholder="Enter course code" required>
                        </div>
    
                        <div class="form-group">
                            <label for="teacher-name">Teacher's Name:</label>
                            <input type="text" id="teacher-name" name="teacher_name" placeholder="Enter teacher's name" required>
                        </div>
    
                        <div class="form-group">
                            <label for="staff-id">Staff ID:</label>
                            <input type="text" id="staff-id" name="staff_id" placeholder="Enter staff ID" required>
                        </div>
    
                        <div class="form-group">
                            <label for="class">Class:</label>
                            <input type="text" id="class" name="class" placeholder="Enter class" required>
                        </div>
    
                        <div class="form-group">
                            <button type="button" class="btn-submit" id="register-button" onclick="registerCourse()">Register Course</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

       <script src="js/scripts.js"></script>
    </body>
</html>

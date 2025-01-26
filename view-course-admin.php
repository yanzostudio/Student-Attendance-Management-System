<?php
// Database connection
$servername = "localhost";
$username = "root"; // your database username
$password = ""; // your database password
$dbname = "sams_db"; // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch courses from the database
$sql = "SELECT * FROM course";
$result = $conn->query($sql);

// Handle course edit and delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit_course'])) {
        // Edit course logic
        $courseCode = $_POST['course_code'];
        $courseName = $_POST['course_name'];
        $teacherName = $_POST['teacher_name'];
        $staffId = $_POST['staff_id'];
        $class = $_POST['class'];

        // Update query (adjust as necessary)
        $updateSql = "UPDATE course SET CourseName = '$courseName', teacherID = '$staffId' WHERE CourseCode = '$courseCode'";
        if ($conn->query($updateSql) === TRUE) {
            echo "Course updated successfully!";
        } else {
            echo "Error updating course: " . $conn->error;
        }
    } elseif (isset($_POST['delete_course'])) {
        // Delete course logic
        $courseCode = $_POST['course_code_to_delete'];

        // Delete query (adjust as necessary)
        $deleteSql = "DELETE FROM course WHERE CourseCode = '$courseCode'";
        if ($conn->query($deleteSql) === TRUE) {
            echo "Course deleted successfully!";
        } else {
            echo "Error deleting course: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin</title>
    <link rel="stylesheet" href="css/view-course-admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .notification {
            display: none;
            margin: 1rem auto;
            padding: 1rem;
            background-color: rgba(71, 192, 255, 0.2);
            color: rgba(71, 192, 255, 1);
            border: 1px solid rgba(71, 192, 255, 1);
            border-radius: 4px;
            text-align: center;
            width: 80%; /* Adjust based on layout */
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php require 'sidebar-admin.php'; ?>

    <!-- Main Content -->
    <div class="main-content"> 
        <!-- Header -->
        <div class="header-wrapper"> 
            <div class="header-title"> 
                <span>Admin</span> 
                <h2>Update Course</h2> 
            </div> 
            <img src="assets/img/manager.png" alt=""> 
        </div> 

        <!-- Notification -->
        <div class="notification" id="notification"></div>

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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row['CourseName'] . "</td>";
                                    echo "<td>" . $row['CourseCode'] . "</td>";
                                    echo "<td>" . $row['teacherID'] . "</td>";
                                    echo "<td>" . $row['teacherID'] . "</td>"; // Assuming teacherID corresponds to Staff ID
                                    echo "<td>Class Name</td>"; // Replace with appropriate class name if needed
                                    echo "<td>
                                        <form method='POST'>
                                            <input type='hidden' name='course_code_to_delete' value='" . $row['CourseCode'] . "'>
                                            <button type='submit' name='delete_course' class='delete-button'><i class='fa fa-trash'></i> Delete</button>
                                        </form>
                                        </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>No courses found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <form class="course-form" method="POST" id="course-form">
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
                        <button type="submit" name="edit_course" class="btn-submit" id="edit-button">Edit Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const courseTable = document.getElementById('course-table').querySelector('tbody');
            const form = document.getElementById('course-form');
            const editButton = document.getElementById('edit-button');
            const notification = document.getElementById('notification');

            let editingRow = null;

            // Show notification
            function showNotification(message) {
                notification.textContent = message;
                notification.style.display = 'block';
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 3000);
            }

            // When the Edit button in the form is clicked
            editButton.addEventListener('click', function () {
                const courseName = form['course_name'].value;
                const courseCode = form['course_code'].value;
                const teacherName = form['teacher_name'].value;
                const staffId = form['staff_id'].value;
                const className = form['class'].value;

                if (editingRow) {
                    editingRow.children[0].textContent = courseName;
                    editingRow.children[1].textContent = courseCode;
                    editingRow.children[2].textContent = teacherName;
                    editingRow.children[3].textContent = staffId;
                    editingRow.children[4].textContent = className;

                    editingRow = null;

                    showNotification('The course has been successfully updated.');
                }

                form.reset();
            });

            courseTable.addEventListener('click', function (event) {
                const target = event.target;
                if (target.closest('.edit-button')) {
                    editingRow = target.closest('tr');

                    form['course_name'].value = editingRow.children[0].textContent;
                    form['course_code'].value = editingRow.children[1].textContent;
                    form['teacher_name'].value = editingRow.children[2].textContent;
                    form['staff_id'].value = editingRow.children[3].textContent;
                    form['class'].value = editingRow.children[4].textContent;
                } else if (target.closest('.delete-button')) {
                    const confirmDelete = confirm('Are you sure you want to delete this course?');
                    if (confirmDelete) {
                        target.closest('tr').remove();
                        showNotification('The course has been successfully deleted.');
                    }
                }
            });
        });
    </script>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>

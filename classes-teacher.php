<?php
    require 'db_config.php'; // Ensure proper connection
    session_start();

    // Check if the teacher is logged in
    if (!isset($_SESSION['teacherID'])) {
        header("Location: login.php");
        exit();
    }

    $teacherID = $_SESSION['teacherID'];
    $successMessage = '';

    // Handle deletion if the delete action is triggered
    if (isset($_GET['delete']) && isset($_GET['classID'])) {
        $classID = intval($_GET['classID']); // Sanitize input

        // Check if the class belongs to the teacher
        $checkQuery = "SELECT * FROM class WHERE ClassID = ? AND TeacherID = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("ii", $classID, $teacherID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Proceed with deletion
            $deleteQuery = "DELETE FROM class WHERE ClassID = ?";
            $stmt = $conn->prepare($deleteQuery);
            $stmt->bind_param("i", $classID);

            if ($stmt->execute()) {
                $successMessage = "Class deleted successfully.";
            } else {
                $successMessage = "Error deleting class.";
            }
        } else {
            $successMessage = "Unauthorized action.";
        }

        $stmt->close();
    }

    // Query to fetch classes for the logged-in teacher
    $sql = "SELECT ClassID, ClassName FROM class WHERE TeacherID = ? ORDER BY ClassID DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $teacherID);
    $stmt->execute();
    $result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Class</title>
        <link rel="stylesheet" href="css/classes-teacher-styles.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            /* Optional: Add styles for success message */
            .notification-message {
                padding: 10px;
                background-color: #4CAF50;
                color: white;
                border-radius: 5px;
                text-align: center;
                margin-top: 20px;
            }
        </style>
    </head>

    <body>
    <?php require 'sidebar-teacher.php'; ?>

        <div class="main-content">
            <div class="header-wrapper">
                <div class="header-title">
                    <span>Teacher</span>
                    <h2>Class</h2>
                </div>
                <img src="images/teacher.png" alt="Teacher">
            </div>
            
            <div class="tabular-wrapper">
                <h3 class="main-title">Classes</h3>

                <!-- Success Message -->
                <?php if (!empty($successMessage)): ?>
                    <div class="notification-message">
                        <?php echo htmlspecialchars($successMessage); ?>
                    </div>
                <?php endif; ?>

                <div class="table-container">
                    <div class="add-course-container">
                        <a href="add-class-teacher.php" class="add-course-btn">Add New Class</a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Bil.</th>
                                <th>Class</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $counter = 1;
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                        <td>" . $counter++ . "</td>
                                        <td>" . htmlspecialchars($row['ClassName']) . "</td>
                                        <td>
                                            <a href='view-class-teacher.php?classID=" . $row['ClassID'] . "'>View</a> | 
                                            <a href='classes-teacher.php?delete=true&classID=" . $row['ClassID'] . "' 
                                               onclick='return confirm(\"Are you sure you want to delete this class?\")'>Delete</a>
                                        </td>
                                    </tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>

<?php
        // Start session
        
        session_start();
        if (($_SESSION['teacherID'])==null) {
            header("Location: login.php");
            exit();
        }

        // Check if the user is logged in
        /*if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            // Redirect to login page if not logged in
            header("Location: login.php");
            exit;
        }*/

        //require 'session.php';
        require 'db_config.php'; // Ensure proper connection

        // Get ClassID from query parameter
        if (isset($_GET['classID']) && is_numeric($_GET['classID'])) {
            $classID = intval($_GET['classID']);

            // Fetch the class name dynamically based on the classID
            $classQuery = "SELECT ClassName FROM class WHERE ClassID = ?";
            $classStmt = $conn->prepare($classQuery);
            $classStmt->bind_param("i", $classID);
            $classStmt->execute();
            $classResult = $classStmt->get_result();

            if ($classResult->num_rows > 0) {
                $classRow = $classResult->fetch_assoc();
                $className = $classRow['ClassName'];
            } else {
                die("Invalid Class ID or Class not found.");
            }
        } else {
            die("Class ID is not provided or invalid.");
        }

        // Query to retrieve class and student data
        $sql = "SELECT s.StudentID, s.username, 
                    CONCAT(FORMAT(SUM(a.AttendanceStatus) / COUNT(a.AttendanceID) * 100, 0), '%') AS AttendanceRate
                FROM student s
                JOIN enroll e ON s.StudentID = e.StudentID
                LEFT JOIN attendance a ON e.StudentID = a.StudentID AND e.ClassID = a.ClassID
                WHERE e.ClassID = ?
                GROUP BY s.StudentID, s.username";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $classID);
        $stmt->execute();
        $result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Class</title>
        <link rel="stylesheet" href="css/classes-teacher-styles.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>

    <body>
    <?php require 'sidebar-teacher.php'; ?>

        <div class="main-content">
            <div class="header-wrapper">
                <div class="header-title">
                    <span>Teacher</span>
                    <h2>Class</h2>
                </div>
                <img src="images/teacher.png" alt="">
            </div>
            
                <div class="tabular-wrapper">
                    <h3 class="main-title">
                            <?php echo htmlspecialchars($className); ?> (Class ID: <?php echo htmlspecialchars($classID); ?>)
                    </h3>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Bil.</th>
                                    <th>Name</th>
                                    <th>Student ID</th>
                                    <th>Percentage</th>
                                </tr>
                                <tbody>
                                <?php
                                        if ($result->num_rows > 0) {
                                            $bil = 1;
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>
                                                    <td>{$bil}</td>
                                                    <td>{$row['username']}</td>
                                                    <td>{$row['StudentID']}</td>
                                                    <td>{$row['AttendanceRate']}</td>
                                                </tr>";
                                                $bil++;
                                            }
                                        } else {
                                            echo "<tr><td colspan='4'>No students found.</td></tr>";
                                        }
                                ?>
                                </tbody>
                            </thead>
                        </table>
                    </div>
                    <div class="button-container"> 
                        <button onclick="window.print()" class="btn print-btn">Print</button>
                        <a href="view-class-teacher.php?classID=<?= $classID ?>" class="btn return-btn">Return</a>
                        <button onclick="toggleSelectMode()" class="btn select-btn">Select</button>
                    </div>
            </div>
        </div>

       <script src="js/scripts.js"></script>
    </body>
</html>
<?php
$stmt->close();
$conn->close();
?>
<?php 
    //require 'session.php';
    require 'db_config.php'; // Ensure proper connection
    session_start();
    if (($_SESSION['studentID'])==null) {
        header("Location: login.php");
        exit();
    }
    
    // Fetch teacher details from the database
    $studentID = $_SESSION['studentID'];
    $query = "SELECT username, Email FROM student WHERE StudentID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $studentID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        $studentName = $student['username'];
        $studentEmail = $student['Email'];
    } else {
        echo "Error fetching student details.";
        exit();}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Student Dashboard</title>
        <link rel="stylesheet" href="css/dashboard-student-style.css" />
        <!--Font Awesome Cdn link-->
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        />
    </head>
    <body>
        <!-- sidebar -->
    <?php require 'sidebar-student.php'; ?>

        <div class="main--content">
            <div class="header--wrapper">
                <div class="header--title">
                    <span>Student</span>
                    <h2>Dashboard</h2>
                </div>
                <div class="user--info">
                    <img src="images/student-icon2.jpg" alt="">
                </div>
            </div>

            <div class="card--container">
                <h3 class="main--title">Menu</h3>
                <div class="card--wrapper">
                    <div class="payment--card light-blue">
                        <div class="card--header">
                            <div class="amount">
                            <span class="title">Profile</span>
                            <span class="title">Name: <?= htmlspecialchars($studentName) ?></span>
                            <span class="title">Teacher ID: <?= htmlspecialchars($studentID) ?></span>
                            <span class="title">Email: <?= htmlspecialchars($studentEmail) ?></span>
                            </div>
                            <i class="fas fa-user icon dark-blue"></i>  
                        </div>
                    </div>

                    <div class="payment--card light-purple">
                        <div class="card--header">
                            <div class="amount">
                              <span class="title">Classes </span> 
                            </div>
                            <i class="fas fa-briefcase icon dark-purple"></i>  
                        </div>
                    </div>

                    <div class="payment--card light-red">
                        <div class="card--header">
                            <div class="amount">
                              <span class="title">Attendance </span> 
                            </div>
                            <i class="fas fa-chart-bar icon dark-red"></i>  
                        </div>
                    </div>
                </div>
            </div>
            <div class="tabular--wrapper">
                <h3 class="main--title">List of Student</h3>
                <div class="table-container">
                    <table>
                        <thead>
                           <tr>
                            <th>Bil</th>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                           </tr>
                           </thead>
                           <tbody>
                            <?php
                            $sql = "SELECT StudentID, username, Email FROM student";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                $bil = 1;
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$bil}</td>
                                            <td>{$row['StudentID']}</td>
                                            <td>{$row['username']}</td>
                                            <td>{$row['Email']}</td>
                                        </tr>";
                                        $bil++;
                                }
                            } else {
                                echo "<tr><td colspan='3'>No students found</td></tr>";
                            }

                            
                            $conn->close();
                            ?>
                           </tbody> 
                           
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>
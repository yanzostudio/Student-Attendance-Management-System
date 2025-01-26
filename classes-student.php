<?php
    //require 'session.php';
    require 'db_config.php'; // Ensure proper connection

    // Fetch class information
    $class_query = "SELECT ClassID, ClassName FROM class";
    $class_result = $conn->query($class_query);

    // Fetch students enrolled in the first class (for simplicity)
    $student_query = "SELECT s.StudentID, s.username, s.Email 
                    FROM student s
                    JOIN enroll e ON s.StudentID = e.StudentID"; // Adjust ClassID dynamically if needed
    $student_result = $conn->query($student_query);

    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Classes</title>
        <link rel="stylesheet" href="css/classes-student-styles.css" />
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
                    <h2>Classes</h2>
                </div>
                <div class="user--info">
                    <img src="images/student-icon2.jpg" alt="">
                </div>
            </div>

            <div class="card--container">
                <h3 class="main--title">Class Info</h3>
                <div class="card--wrapper">
                <?php if ($class_result->num_rows > 0): ?>
                    <?php while ($class = $class_result->fetch_assoc()): ?>
                        <div class="payment--card light-blue">
                            <div class="card--header">
                                <div class="amount">
                                    <span class="title"><?php echo $class['ClassName']; ?></span>
                                </div>
                                <i class="fas fa-users icon dark-blue"></i>  
                            </div>
                        </div>
                    <?php endwhile; ?>
                    <?php else: ?>
                        <p>No classes found.</p>
                <?php endif; ?>   
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
                           <?php if ($student_result->num_rows > 0): ?>
                                <?php $bil = 1; ?>
                                <?php while ($student = $student_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $bil++; ?></td>
                                        <td><?php echo $student['StudentID']; ?></td>
                                        <td><?php echo $student['username']; ?></td>
                                        <td><?php echo $student['Email']; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4">No students found.</td>
                                    </tr>
                                <?php endif; ?>
                           </tbody> 
                           
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>
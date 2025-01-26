<?php

    
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "sams_db"; // Database name
    //include("db_config.php");    

    // Database connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    // $sqlStud = "SELECT * FROM student ORDER BY StudentID DESC LIMIT 2";
    // $resultStud = $conn->query($sqlStud);

    // $sqlTeach = "SELECT * FROM teacher ORDER BY teacherID DESC LIMIT 2";
    // $resultTeach = $conn->query($sqlTeach);

    $limit = 4; // Number of rows to fetch
    $sqlStud = "SELECT * FROM student ORDER BY StudentID DESC LIMIT ?";
    $stmtStud = $conn->prepare($sqlStud);
    $stmtStud->bind_param("i", $limit);
    $stmtStud->execute();
    $resultStud = $stmtStud->get_result();

    $sqlTeach = "SELECT * FROM teacher ORDER BY teacherID DESC LIMIT ?";
    $stmtTeach = $conn->prepare($sqlTeach);
    $stmtTeach->bind_param("i", $limit);
    $stmtTeach->execute();
    $resultTeach = $stmtTeach->get_result();



    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        if($_POST['type']=="student"){
            if (!empty($_POST['name']) && !empty($_POST['susername']) && !empty($_POST['password']) && !empty($_POST['Email']) && isset($_POST['Status'])) {
                $name = $_POST['name'];
                $susername = $_POST['susername'];
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $Email = $_POST['Email'];
                $Status = $_POST['Status'];

                // Prepare SQL query using placeholders
                $stmt = $conn->prepare("INSERT INTO student (name, username, password, Email, Status) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssi", $name , $susername, $password, $Email, $Status); 

                // Execute prepared statement
                if ($stmt->execute()) {
                    header('Location: registeruser-admin.php');
                    exit;
                } else {
                    echo "Error: " . $stmt->error;
                }

                // Close statement and connection
                $stmt->close();
            }
        }
        else if($_POST['type']=="teacher"){
            if (!empty($_POST['name']) && !empty($_POST['susername']) && !empty($_POST['password']) && !empty($_POST['Email']) && isset($_POST['Status']) && !empty($_POST['phoneNo'])) {
                $name = $_POST['name'];
                $susername = $_POST['susername'];
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $Email = $_POST['Email'];
                $Status = $_POST['Status'];
                $phoneNo = $_POST['phoneNo'];

                // Prepare SQL query using placeholders
                $stmt = $conn->prepare("INSERT INTO teacher (name, username, password, Email, Status, phoneNo) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssi", $name, $susername, $password, $Email, $Status, $phoneNo);

                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


                // Execute prepared statement
                if ($stmt->execute()) {
                    header('Location: registeruser-admin.php');
                    exit;
                } else {
                    echo "Error: " . $stmt->error;
                }

                // Close statement and connection
                $stmt->close();
            }
        }

        $conn->close();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register User Page</title>
    <link rel="stylesheet" href="css/regUser-admin-styles.css">
    <!--Font Awesome Cdn link-->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        />
</head>
  <body>
    <!-- sidebar -->
  <?php require 'sidebar-admin.php'; ?>

    <!-- Main Content -->
    <div class="main--content">
        <div class="header--wrapper">
            <div class="header--title">
                <span>Admin</span>
                <h2>Register User</h2>
            </div>
            <div class="user--info">
                <img src="images/manager.png">
            </div>
        </div>

        <div class="content">
            <!-- Teacher and Student Tables -->
            <div class="teacher-student-wrapper">
                <!-- Teacher Section -->
                <section class="teacher-list">
                    <h2>Teachers</h2>
                    <a href="admin-manage-teacher.php">View All</a>
                    <table class="table" id="studentTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Teacher ID</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($resultTeach->num_rows > 0) {
                           while($row = $resultTeach->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['TeacherID']) . "</td>"; 
                                echo "<td>" . htmlspecialchars($row['Email']) . "</td>"; 
                                echo "<td>" . "<a href='view-teacher-admin.php?TeacherID=" . $row['TeacherID'] . "'>View</a>" . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No records found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                </section>
        
                <!-- Student Section -->
                <section class="student-list">
                    <h2>Students</h2>
                    <a href="admin-manage-student.php">View All</a>
                    <table class="table" id="studentTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Student ID</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($resultStud->num_rows > 0) {
                           while($row = $resultStud->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['StudentID']) . "</td>"; 
                                echo "<td>" . htmlspecialchars($row['Email']) . "</td>"; 
                                echo "<td>" . "<a href='view-student-admin.php?StudentID=" . $row['StudentID'] . "'>View</a>" . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No records found</td></tr>";
                        }

                        $conn->close();
                        ?>
                    </tbody>
                </table>
                </section>
            </div>

            <!-- Register Form -->
            <div class="register-wrapper">
                <h2>Register User</h2>
                <form action="registeruser-admin.php" method="POST">
                    <div>
                    <input type="radio" id="Student" name="type" value="student" required>
                    <label for="Student">Student</label>
                    <input type="radio" id="Teacher" name="type" value="teacher" required>
                    <label for="Teacher">Teacher</label>
                    </div>
                    <div class="form-group">
                        <label >Name</label>
                        <input type="text"  name="name" required/>
                    </div>
                    <div class="form-group">
                        <label >Username</label>
                        <input type="text"  name="susername" required/>
                    </div>
                    <div class="form-group">
                        <label >Password</label>
                        <input type="text"  name="password" required />
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="Email"  name="Email" required />
                    </div>
                    <div class="form-group">
                        <label>Phone No</label>
                        <input type="tel"  name="phoneNo"/>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="Status">
                            <option value="0" name="Status">Inactive</option>
                            <option value="1" name="Status">Active</option>
                        </select>
                    </div>
                    <div class="form-group">
                    <input type="submit" value="Add User">
                    <input type="reset" value="Reset">
                    </div>
                </form>
            </div>
        </div>       
    </body>
</html>

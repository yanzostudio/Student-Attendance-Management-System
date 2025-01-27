<?php
require 'db_config.php';

    $limit = 10; // Number of rows to fetch
    $sqlStud = "SELECT * FROM (SELECT * FROM students ORDER BY Student_ID DESC) WHERE ROWNUM <= :limit";
    $stmtStud = oci_parse($conn, $sqlStud);
    oci_bind_by_name($stmtStud, ":limit", $limit);
    oci_execute($stmtStud);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        if ($_POST['type'] == "student") {
            if (!empty($_POST['Email']) && !empty($_POST['password'])) {
                $name = $_POST['name'];
                $contactNo = $_POST['contactNo'];
                $password = $_POST['password'];
                $Email = $_POST['Email'];
                $dob = $_POST['dob'];  // This is in 'YYYY-MM-DD' format from the <input type="date"> field
                $gender = $_POST['gender'];
    
                // Prepare SQL query using placeholders
                // Convert 'YYYY-MM-DD' to the appropriate Oracle Date format
                $stmt = oci_parse($conn, "INSERT INTO students (student_name, student_email, student_password, student_contactNo, student_DATEOFBIRTH, student_gender) 
                                          VALUES (:name, :Email, :password, :contactNo, TO_DATE(:dob, 'YYYY-MM-DD'), :gender)");
                
                // Bind the values
                oci_bind_by_name($stmt, ":name", $name);
                oci_bind_by_name($stmt, ":Email", $Email);
                oci_bind_by_name($stmt, ":password", $password);
                oci_bind_by_name($stmt, ":contactNo", $contactNo);
                oci_bind_by_name($stmt, ":dob", $dob); // Directly bind the date in 'YYYY-MM-DD' format
                oci_bind_by_name($stmt, ":gender", $gender);
    
                // Execute prepared statement
                if (oci_execute($stmt)) {
                    header('Location: registeruser-admin.php');
                    exit;
                } else {
                    $e = oci_error($stmt);
                    echo "Error: " . $e['message'];
                }
    
                // Close statement
                oci_free_statement($stmt);
            }
        }
        oci_close($conn); // Close connection
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Student Page</title>
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
                <h2>Register Student</h2>
            </div>
            <div class="user--info">
                <img src="images/manager.png">
            </div>
        </div>

        <div class="content">
            <!-- Teacher and Student Tables -->
            <div class="teacher-student-wrapper">
                <!-- Student Section -->
                <section class="student-list">
                    <h2>Students</h2>
                    <a href="admin-manage-student.php">View All</a>
                    <table class="table" id="studentTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>contactNo</th>
                            <th>Student ID</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($row = oci_fetch_assoc($stmtStud)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['STUDENT_NAME']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['STUDENT_CONTACTNO']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['STUDENT_ID']) . "</td>"; 
                            echo "<td>" . htmlspecialchars($row['STUDENT_EMAIL']) . "</td>"; 
                            echo "<td>" . "<a href='view-student-admin.php?Student_ID=" . $row['STUDENT_ID'] . "'>View</a>" . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
                </section>
            </div>

            <!-- Register Form -->
            <div class="register-wrapper">
                <h2>Register Student</h2>
                <form action="registeruser-admin.php" method="POST">
                    <div>
                    <input type="radio" id="Student" name="type" value="student" required>
                    <label for="Student">Student</label>
                    </div>
                    <div class="form-group">
                        <label >Name</label>
                        <input type="text"  name="name" required/>
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
                        <label>Contact No</label>
                        <input type="tel"  name="contactNo"/>
                    </div>
                    <div class="form-group">
                        <label>DOB</label>
                        <input type="date"  name="dob"/>
                    </div>
                    <div class="form-group">
                    <label for="gender">Gender:</label>
                    <select name="gender" id="gender">
                    <option value="M">Male</option>
                    <option value="F">Female</option>   </select>
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

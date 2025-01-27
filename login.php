<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STUDENT ATTENDANCE MANAGEMENT SYSTEM</title>
    <link rel="stylesheet" href="css/login-page-styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body {
            background: url('/images/aisams_bg.png') no-repeat center center fixed;
            background-size: cover;
        }
    </style>
</head>
<body>
<div class="container">
    
    <div id="login-section">
        <form action="login.php" method="POST">
   
            <div class="top">
                <header>Student Attendance <br> Management System</header>
            </div>
            <div class="logo"></div>

            <div class="two-forms">
                <div class="role-user">
                    <input class="role_input" type="radio" id="student" name="type" value="student" required>
                    <label class="role_label" for="student">Student</label><br>
                    <input class="role_input" type="radio" id="teacher" name="type" value="teacher" required>
                    <label class="role_label" for="teacher">Teacher</label><br>
                    <input class="role_input" type="radio" id="admin" name="type" value="admin" required>
                    <label class="role_label" for="admin">Admin</label><br>
                </div>

                <div class="input-box">
                    <input type="text" name="email" class="input-field" placeholder="email">
                    <i class="bx bx-user"></i>
                </div>
            
                <div class="input-box">
                    <input type="password" name="password" class="input-field" placeholder="Password">
                    <i class="bx bx-lock-alt"></i>
                </div>
            
                <div class="input-box flex justify-between items-center gap-4">
                    <input type="submit" class="submit" value="Submit">
                    <input type="reset" class="reset" value="Reset">
                </div>

            </div>
        </form>
    </div>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servername = "localhost/XE";
    $username = "dbSams";  // Oracle DB username
    $password = "system";  // Oracle DB password

    // Connect to Oracle Database
    $conn = oci_connect($username, $password, $servername);
    if (!$conn) {
        $e = oci_error();
        die("Connection failed: " . $e['message']);
    }

    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $type = $_POST['type'];

        // Query the appropriate table based on user type
        if ($type == "student") {
            $query = "SELECT * FROM students WHERE STUDENT_EMAIL = :email";
        } else if ($type == "teacher") {
            $query = "SELECT * FROM staffs  WHERE STAFF_EMAIL = :email";
        } else if ($type == "admin") {
            $query = "SELECT * FROM staffs  WHERE STAFF_EMAIL = :email";
        }

        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":email", $email);
        oci_execute($stmt);

        $row = oci_fetch_assoc($stmt);
        if ($row) {
            session_start();
            if ($type == "student") {
            if ($password == $row['STUDENT_PASSWORD']) {
                
                
                    $_SESSION['studentID'] = $row['STUDENT_ID'];
                header('Location: dashboard-student.php');
                    exit();
                }else {
                    echo "<script>alert('Invalid password.');</script>";
                }
                
            }
            else if($type == "teacher" || $type == "admin"){
                if ($password == $row['STAFF_PASSWORD']) {

                    if ($type == "teacher") {
                        $_SESSION['teacherID'] = $row['STAFF_ID'];
                        header('Location: dashboard-teacher.php');
                        exit();
                    } else if ($type == "admin") {
                        $_SESSION['AdminID'] = $row['STAFF_ID'];
                        header('Location: dashboard-admin.php');
                        exit();
                    }
                }else {
                    echo "<script>alert('Invalid password.');</script>";
                }
            }
        } else {
            echo "<script>alert('Invalid email.');</script>";
        }

        oci_free_statement($stmt);
    }
    oci_close($conn);
}
?>

</body>
</html>

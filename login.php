<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI-Enhanced Student Attendance Management System</title>
    <link rel="stylesheet" href="css/login-page-styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<div class="container">

    <div id="login-section">
        <form action="login.php" method="POST">
   
            <div class="top">
                <header>AI - Student Attendance <br> Management System</header>
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
                    <input type="text" name="username" class="input-field" placeholder="Username">
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
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "sams_db"; // Database name

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $type = $_POST['type'];

        // Query the appropriate table based on user type
        if ($type == "student") {
            $stmt = $conn->prepare("SELECT * FROM student WHERE username = ?");
        } else if ($type == "teacher") {
            $stmt = $conn->prepare("SELECT * FROM teacher WHERE username = ?");
        } else if ($type == "admin") {
            $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Verify the entered password with the hashed password
            if (password_verify($password, $row['password'])) {
                session_start();
                if ($type == "student") {
                    $_SESSION['studentID'] = $row['StudentID'];
                    header('Location: dashboard-student.php');
                    exit();
                } else if ($type == "teacher") {
                    $_SESSION['teacherID'] = $row['TeacherID'];
                    header('Location: dashboard-teacher.php');
                    exit();
                } else if ($type == "admin") {
                    $_SESSION['AdminID'] = $row['AdminID'];
                    header('Location: dashboard-admin.php');
                    exit();
                }
            } else {
                echo "<script>alert('Invalid password.');</script>";
            }
        } else {
            echo "<script>alert('Invalid username.');</script>";
        }

        $stmt->close();
    }
    $conn->close();
}
?>

</body>
</html>
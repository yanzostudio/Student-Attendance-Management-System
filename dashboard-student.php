<?php 
    // Include the database configuration file
    require 'db_config.php'; // Ensure proper Oracle connection
    session_start();
    
    // Check if studentID is in session, if not redirect to login page
    if (($_SESSION['studentID']) == null) {
        header("Location: login.php");
        exit();
    }

    // Fetch student details from the database using Oracle OCI functions
    $studentID = $_SESSION['studentID'];
    $query = "SELECT STUDENT_NAME, STUDENT_EMAIL FROM STUDENTS WHERE STUDENT_ID = :studentID";
    $stmt = oci_parse($conn, $query); // Use oci_parse for Oracle queries
    oci_bind_by_name($stmt, ":studentID", $studentID); // Bind the studentID
    oci_execute($stmt); // Execute the query

    $student = oci_fetch_assoc($stmt); // Fetch result

    if ($student) {
        $studentName = $student['STUDENT_NAME']; // Note the correct column name
        $studentEmail = $student['STUDENT_EMAIL']; // Note the correct column name
    } else {
        echo "Error fetching student details.";
        exit();
    }
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
                            <span class="title">Student ID: <?= htmlspecialchars($studentID) ?></span>
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
            <h3 class="main--title">List of Students</h3>
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
                        // Fetch list of all students from Oracle
                        $sql = "SELECT STUDENT_ID, STUDENT_NAME, STUDENT_EMAIL FROM STUDENTS";
                        $stmt = oci_parse($conn, $sql); // Use oci_parse for Oracle queries
                        oci_execute($stmt); // Execute the query

                        $bil = 1;
                        while ($row = oci_fetch_assoc($stmt)) {
                            echo "<tr>
                                    <td>{$bil}</td>
                                    <td>{$row['STUDENT_ID']}</td>
                                    <td>{$row['STUDENT_NAME']}</td>
                                    <td>{$row['STUDENT_EMAIL']}</td>
                                </tr>";
                            $bil++;
                        }
                        ?>
                    </tbody> 
                </table>
            </div>
        </div>
    </div>
</body>
</html>

<?php 
    // Close the Oracle connection
    oci_free_statement($stmt);
    oci_close($conn);
?>

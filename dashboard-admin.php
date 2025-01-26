<?php
// Database configuration
$host = '127.0.0.1';
$username = 'root';
$password = ''; // Replace with your database password
$dbname = 'sams_db';

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch admin data
$adminQuery = "SELECT COUNT(*) AS total_users FROM admins";
$adminResult = $conn->query($adminQuery);
$totalUsers = $adminResult->fetch_assoc()['total_users'];

// Fetch attendance statistics
$attendanceQuery = "SELECT c.ClassName, COUNT(a.AttendanceID) AS count, 
                            ROUND((COUNT(a.AttendanceID) / (
                                SELECT COUNT(*) FROM attendance WHERE ClassID = c.ClassID
                            )) * 100, 2) AS percentage
                     FROM attendance a
                     JOIN class c ON a.ClassID = c.ClassID
                     GROUP BY c.ClassName";
$attendanceResult = $conn->query($attendanceQuery);

// Attendance data for table
$attendanceData = [];
while ($row = $attendanceResult->fetch_assoc()) {
    $attendanceData[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/dashboard-admin-style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
    <!-- sidebar -->
    <?php require 'sidebar-admin.php'; ?>

    <div class="main--content">
        <div class="header--wrapper">
            <div class="header--title">
                <span>Admin</span>
                <h2>Dashboard</h2>
            </div>
            <div class="user--info">
                <img src="images/manager.png" alt="Admin" />
            </div>
        </div>

        <div class="card--container">
            <h3 class="main--title">Menu</h3>
            <div class="card--wrapper">
                <div class="payment--card light-blue">
                    <div class="card--header">
                        <div class="amount">
                            <span class="title">User</span>
                            <span class="card-detail">Total Users: <?php echo $totalUsers; ?></span>
                        </div>
                        <i class="fas fa-user icon dark-blue"></i>
                    </div>
                </div>

                <div class="payment--card light-purple">
                    <div class="card--header">
                        <div class="amount">
                            <span class="title">Schedule</span>
                        </div>
                        <i class="fas fa-briefcase icon dark-purple"></i>
                    </div>
                </div>

                <div class="payment--card light-red">
                    <div class="card--header">
                        <div class="amount">
                            <span class="title">Attendance</span>
                        </div>
                        <i class="fas fa-chart-bar icon dark-red"></i>
                    </div>
                </div>

                <div class="table-container">
                    <h2>Student Attendance Statistics Table Overview</h2>
                    <table class="stats-table">
                        <thead>
                            <tr>
                                <th>Class Name</th>
                                <th>Count</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attendanceData as $data): ?>
                                <tr>
                                    <td><?php echo $data['ClassName']; ?></td>
                                    <td><?php echo $data['count']; ?></td>
                                    <td><?php echo $data['percentage']; ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>Total</td>
                                <td><?php echo array_sum(array_column($attendanceData, 'count')); ?></td>
                                <td>100%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>

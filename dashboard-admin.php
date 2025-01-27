<?php
require 'db_config.php';

// Fetch admin data (total users)
$adminQuery = "SELECT COUNT(*) AS total_users FROM administrators";
$adminResult = oci_parse($conn, $adminQuery);
oci_execute($adminResult);
$row = oci_fetch_assoc($adminResult);
$totalUsers = $row['TOTAL_USERS'];  // Oracle returns column names in uppercase by default

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

            </div>
        </div>
    </div>
</body>
</html>

<?php
// Free Oracle resources and close the connection
oci_free_statement($adminResult);
oci_close($conn);
?>

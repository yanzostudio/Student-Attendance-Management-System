<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Student Overview</title> 
    <link rel="stylesheet" href="css/student overview-admin.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
</head> 

<body> 
    <!-- Sidebar -->
    <?php 
require 'sidebar-admin.php';
?>

    <!-- Main Content -->
    <div class="main-content"> 
        <!-- Header -->
        <div class="header-wrapper"> 
            <div class="header-title"> 
                <span>Admin</span> 
                <h2>Attendance</h2> 
            </div> 
            <img src="images/manager.png"> 
        </div> 

       <div class="overview-section">
    <!-- Graph Container -->
    <div class="graph-container">
        <h1>AI-Enhanced Student Attendance Management System</h1>
        <h2>Student Overview</h2>

        <div class="chart-container" style="display: flex; justify-content: center; gap: 2rem; margin-top: 2rem;">
            <!-- Chart Containers -->
            <div style="width: 300px;">
                <canvas id="chartJan"></canvas>
            </div>
            <div style="width: 300px;">
                <canvas id="chartFeb"></canvas>
            </div>
            <div style="width: 300px;">
                <canvas id="chartMar"></canvas>
            </div>
        </div>

    </div>
</div>
    <!-- Chart.js Script -->
    <script>
        // Function to create chart configurations
        const chartConfig = (month, data) => ({
            type: 'bar',
            data: {
                labels: ['Class A', 'Class B', 'Class C', 'Class D'],
                datasets: [{
                    label: month,
                    data: data,
                    backgroundColor: '#4CA8F7',
                    borderColor: '#4CA8F7',
                    borderWidth: 1,
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });

        // Initialize Charts
        new Chart(document.getElementById('chartJan'), chartConfig('January', [80, 90, 70, 100]));
        new Chart(document.getElementById('chartFeb'), chartConfig('February', [85, 95, 75, 95]));
        new Chart(document.getElementById('chartMar'), chartConfig('March', [90, 100, 80, 85]));
    </script>
</body> 
</html>

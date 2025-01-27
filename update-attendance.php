<?php
require 'db_config.php'; // Database connection

// Ensure the user is logged in (optional)
session_start();
if (!isset($_SESSION['studentID'])) {
    die("Access denied. Please log in.");
}

$studentID = $_SESSION['studentID'];

// Get the JSON input from the client
$inputData = json_decode(file_get_contents('php://input'), true);
$studentIDToUpdate = $inputData['studentID'];
$classIDToUpdate = $inputData['classID'];

// Check if parameters are provided
if (!$studentIDToUpdate || !$classIDToUpdate) {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    exit;
}

// Oracle Database connection
$conn = oci_connect($username, $password, $servername);
if (!$conn) {
    $e = oci_error();
    echo json_encode(['status' => 'error', 'message' => 'Oracle Connection failed: ' . $e['message']]);
    exit;
}

// SQL query to update the attendance
$sql = "UPDATE ATTENDANCE SET ATTENDED = 'Y' 
        WHERE STUDENT_ID = :studentID AND CLASS_ID = :classID AND ATTENDED = 'N'";

// Prepare and execute the query
$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ":studentID", $studentIDToUpdate);
oci_bind_by_name($stid, ":classID", $classIDToUpdate);

if (oci_execute($stid)) {
    echo json_encode(['status' => 'success']);
} else {
    $e = oci_error($stid);
    echo json_encode(['status' => 'error', 'message' => 'Error updating attendance: ' . $e['message']]);
}

// Close the connection
oci_free_statement($stid);
oci_close($conn);
?>

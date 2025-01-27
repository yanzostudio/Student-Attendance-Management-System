<?php

    $servername = "localhost/XE";
    $username = "dbSams";  // Oracle DB username
    $password = "system";  // Oracle DB password

    // Connect to Oracle Database
    $conn = oci_connect($username, $password, $servername);
    
    if (!$conn) {
        $e = oci_error();
        die("Connection failed: " . $e['message']);
    }
?>

<?php
$host = 'localhost';       // or the IP address of your DB server
$user = 'root';            // your database username
$password = '';            // your database password
$database = 'gms';      // your database name

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
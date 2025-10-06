<?php
$servername = "127.0.0.1"; // Use IP address if connecting from the host
$username = "root";
$password = "root"; // Password used with the MYSQL_ROOT_PASSWORD environment variable
$dbname = "web"; // Ensure this matches the actual database name

try {
    $conn = new PDO("mysql:host=$servername;port=3310;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully"; // Σχολίασε ή αφαίρεσε αυτή τη γραμμή
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

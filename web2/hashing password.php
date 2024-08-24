<?php
// Connect to your database
include 'db_connection.php';

$sql = "SELECT id, password_hash FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $plain_password = $row['password_hash']; // Assuming the passwords are stored in plain text

        // Hash the password
        $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

        // Update the hashed password back in the database
        $update_sql = "UPDATE users SET password_hash='$hashed_password' WHERE id=$id";
        if ($conn->query($update_sql) === TRUE) {
            echo "Password for user ID $id updated successfully.<br>";
        } else {
            echo "Error updating password for user ID $id: " . $conn->error . "<br>";
        }
    }
} else {
    echo "0 results";
}

$conn->close();

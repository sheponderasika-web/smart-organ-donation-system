<?php
include 'db.php';

// Old table drop karun navin structure sobat table banvuyat
$conn->query("DROP TABLE IF EXISTS receivers");

$sql = "CREATE TABLE receivers (
    receiver_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    gender VARCHAR(10) NOT NULL,
    blood_group VARCHAR(5) NOT NULL,
    required_organ VARCHAR(50) NOT NULL,
    contact VARCHAR(15) NOT NULL,
    hospital_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "<h2 style='color:green;'>Receivers table fixed successfully!</h2>";
    echo "<a href='receiver_add.php'>Go back to Add Receiver Page</a>";
} else {
    echo "Error: " . $conn->error;
}
?>
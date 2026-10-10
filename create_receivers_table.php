<?php
include 'db.php';

$sql = "CREATE TABLE IF NOT EXISTS receivers (
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
    echo "<h2>Success! Table 'receivers' created successfully.</h2>";
    echo "<a href='dashboard.php'>Go to Dashboard</a>";
} else {
    echo "Error creating table: " . $conn->error;
}
?>
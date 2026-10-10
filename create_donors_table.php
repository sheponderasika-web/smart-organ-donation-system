<?php

include 'db.php';

$sql = "CREATE TABLE IF NOT EXISTS donors (
    donor_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    gender VARCHAR(10) NOT NULL,
    blood_group VARCHAR(5) NOT NULL,
    organ VARCHAR(50) NOT NULL,
    contact VARCHAR(15) NOT NULL,
    location VARCHAR(100) NOT NULL,
    availability VARCHAR(20) NOT NULL DEFAULT 'Available',
    verification_status VARCHAR(30) NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "<h2>Success! Donors table created successfully.</h2>";
    echo "<a href='dashboard.php'>Go to Dashboard</a>";
} else {
    echo "Error creating donors table: " . $conn->error;
}

?>
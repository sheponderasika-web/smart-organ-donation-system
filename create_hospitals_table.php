<?php

include 'db.php';

$sql = "CREATE TABLE IF NOT EXISTS hospitals (
    hospital_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    contact VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {

    echo "<h2>Success!</h2>";
    echo "<p>Hospitals table is ready.</p>";
    echo "<a href='hospital_add.php'>Go to Add Hospital</a>";

} else {

    echo "<h2>Error</h2>";
    echo "<p>" . $conn->error . "</p>";
}

?>
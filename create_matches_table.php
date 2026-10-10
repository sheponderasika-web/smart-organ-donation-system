<?php

include "db.php";

$sql = "CREATE TABLE IF NOT EXISTS matches (
    match_id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT NOT NULL,
    receiver_id INT NOT NULL,
    organ VARCHAR(100) NOT NULL,
    blood_group_match VARCHAR(50) NOT NULL,
    location_match VARCHAR(50) NOT NULL,
    availability_match VARCHAR(50) NOT NULL,
    match_score INT NOT NULL,
    match_status VARCHAR(50) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $sql)) {
    echo "<h2 style='color:green;'>✅ Matches table created successfully!</h2>";
    echo "<p>Ab phpMyAdmin me jaakar matches table check karo.</p>";
} else {
    echo "<h2 style='color:red;'>❌ Error creating matches table</h2>";
    echo "<p>" . htmlspecialchars(mysqli_error($conn)) . "</p>";
}

?>
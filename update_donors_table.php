<?php

include 'db.php';

$sql = "ALTER TABLE donors
ADD COLUMN location VARCHAR(100) NOT NULL AFTER contact,
ADD COLUMN availability VARCHAR(20) NOT NULL DEFAULT 'Available' AFTER location,
ADD COLUMN verification_status VARCHAR(30) NOT NULL DEFAULT 'Pending' AFTER availability";

if ($conn->query($sql) === TRUE) {
    echo "<h2>Success!</h2>";
    echo "<p>Donors table successfully updated.</p>";
    echo "<a href='donor_list.php'>Open Donor List</a>";
} else {
    echo "<h2>Error</h2>";
    echo "<p>" . $conn->error . "</p>";
}

?>
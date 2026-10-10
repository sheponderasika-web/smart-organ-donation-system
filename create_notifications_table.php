<?php

include 'db.php';

$sql = "CREATE TABLE IF NOT EXISTS notifications (

    notification_id INT AUTO_INCREMENT PRIMARY KEY,

    match_id INT NOT NULL,

    message VARCHAR(255) NOT NULL,

    notification_status VARCHAR(20) DEFAULT 'Unread',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

)";

if ($conn->query($sql) === TRUE) {

    echo "Notifications table created successfully!";

} else {

    echo "Error creating notifications table: "
         . $conn->error;

}

?>
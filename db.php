<?php
$conn = new mysqli("localhost", "root", "", "organ_donation");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
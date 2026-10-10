<?php

session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    echo "<h2>Access Denied! Please <a href='login.php'>Login First</a></h2>";
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid hospital ID.");
}

$id = (int) $_GET['id'];

$stmt = $conn->prepare(
    "DELETE FROM hospitals WHERE hospital_id = ?"
);

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    header("Location: hospital_list.php");
    exit();

} else {

    echo "Error deleting hospital: " . $stmt->error;
}

$stmt->close();
$conn->close();

?>
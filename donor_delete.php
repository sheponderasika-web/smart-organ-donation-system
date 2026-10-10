<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // 'id' chya aevaji 'donor_id'
    $sql = "DELETE FROM donors WHERE donor_id = '$id'";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: donor_list.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    header("Location: donor_list.php");
    exit();
}
?>
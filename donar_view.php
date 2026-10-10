<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$result = $conn->query("SELECT * FROM donors");
?>
<!DOCTYPE html>
<html>
<head><title>View Donors</title></head>
<body>
    <h2>All Donors</h2>
    <p><a href="dashboard.php">Back to Dashboard</a> | <a href="donor_add.php">Add New Donor</a></p>

    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th><th>Name</th><th>Age</th><th>Gender</th>
            <th>Blood Group</th><th>Organ</th><th>Contact</th><th>Status</th><th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['donor_id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['age']; ?></td>
            <td><?php echo $row['gender']; ?></td>
            <td><?php echo $row['blood_group']; ?></td>
            <td><?php echo $row['organ']; ?></td>
            <td><?php echo $row['contact']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td>
                <a href="donor_edit.php?id=<?php echo $row['donor_id']; ?>">Edit</a> | 
                <a href="donor_delete.php?id=<?php echo $row['donor_id']; ?>" onclick="return confirm('Delete this donor?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
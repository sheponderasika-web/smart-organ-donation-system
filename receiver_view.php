<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    echo "<h2>Access Denied! Please <a href='login.php'>Login First</a></h2>";
    exit();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT * FROM receivers WHERE 1=1";

if (!empty($search)) {
    $sql .= " AND (name LIKE '%$search%' OR required_organ LIKE '%$search%' OR hospital_name LIKE '%$search%')";
}

$sql .= " ORDER BY receiver_id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Receiver List</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 30px; }
        .container { max-width: 1100px; margin: auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        .btn { display: inline-block; text-decoration: none; padding: 8px 12px; border-radius: 4px; color: white; font-size: 14px; border: none; cursor: pointer; }
        .btn-back { background-color: #333; margin-bottom: 20px; }
        .btn-search { background-color: #0275d8; }
        .btn-reset { background-color: #6c757d; }
        .search-box { background: #e9ecef; padding: 15px; border-radius: 6px; margin-bottom: 20px; display: flex; gap: 10px; }
        .search-box input[type="text"] { padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; flex-grow: 1; }
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #0275d8; color: white; padding: 12px; }
        td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .no-data { text-align: center; padding: 20px; color: #777; }
    </style>
</head>
<body>

<div class="container">
    <h2>Receiver List</h2>

    <a class="btn btn-back" href="dashboard.php">← Back to Dashboard</a>

    <form method="GET" action="" class="search-box">
        <input type="text" name="search" placeholder="Search by Name, Organ, or Hospital..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-search">🔍 Search</button>
        <a href="receiver_list.php" class="btn btn-reset">Reset</a>
    </form>

    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Blood Group</th>
                <th>Required Organ</th>
                <th>Contact</th>
                <th>Hospital</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['receiver_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['age']); ?></td>
                    <td><?php echo htmlspecialchars($row['gender']); ?></td>
                    <td><?php echo htmlspecialchars($row['blood_group']); ?></td>
                    <td><?php echo htmlspecialchars($row['required_organ']); ?></td>
                    <td><?php echo htmlspecialchars($row['contact']); ?></td>
                    <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <div class="no-data">No receivers found.</div>
    <?php endif; ?>
</div>

</body>
</html>
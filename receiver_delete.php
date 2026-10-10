<?php

echo "<h1 style='color:red;'>NEW RECEIVER LIST FILE</h1>";

session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    echo "<h2>Access Denied! Please <a href='login.php'>Login First</a></h2>";
    exit();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$blood_group = isset($_GET['blood_group']) ? trim($_GET['blood_group']) : '';
$urgency = isset($_GET['urgency']) ? trim($_GET['urgency']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';

$sql = "SELECT * FROM receivers WHERE 1=1";

$params = [];
$types = "";

if ($search !== '') {

    $sql .= " AND (
        name LIKE ?
        OR required_organ LIKE ?
        OR hospital_name LIKE ?
        OR location LIKE ?
    )";

    $searchValue = "%" . $search . "%";

    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;

    $types .= "ssss";
}

if ($blood_group !== '') {

    $sql .= " AND blood_group = ?";

    $params[] = $blood_group;

    $types .= "s";
}

if ($urgency !== '') {

    $sql .= " AND urgency = ?";

    $params[] = $urgency;

    $types .= "s";
}

if ($status !== '') {

    $sql .= " AND status = ?";

    $params[] = $status;

    $types .= "s";
}

$sql .= " ORDER BY receiver_id DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>

<head>

    <title>Receiver List</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 1400px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 4px;
            color: white;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }

        .btn-back {
            background-color: #333;
            margin-bottom: 20px;
        }

        .btn-search {
            background-color: #0275d8;
        }

        .btn-reset {
            background-color: #6c757d;
        }

        .search-box {
            background: #e9ecef;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;

            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .search-box input,
        .search-box select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .search-input {
            flex-grow: 1;
            min-width: 200px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #0275d8;
            color: white;
            padding: 12px;
        }

        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .edit-btn {
            color: #0275d8;
            text-decoration: none;
            font-weight: bold;
            margin-right: 10px;
        }

        .edit-btn:hover {
            text-decoration: underline;
        }

        .delete-btn {
            color: #dc3545;
            text-decoration: none;
            font-weight: bold;
        }

        .delete-btn:hover {
            text-decoration: underline;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #777;
        }

        .action-cell {
            white-space: nowrap;
        }

    </style>

</head>

<body>

<div class="container">

    <h2>Receiver List</h2>

    <a class="btn btn-back" href="dashboard.php">
        ← Back to Dashboard
    </a>


    <form method="GET" action="" class="search-box">

        <input
            class="search-input"
            type="text"
            name="search"
            placeholder="Search Name, Organ, Hospital, Location..."
            value="<?php echo htmlspecialchars($search); ?>"
        >


        <select name="blood_group">

            <option value="">All Blood Groups</option>

            <?php

            $blood_groups = [
                'A+',
                'A-',
                'B+',
                'B-',
                'O+',
                'O-',
                'AB+',
                'AB-'
            ];

            foreach ($blood_groups as $bg) {

                $selected = ($blood_group === $bg)
                    ? 'selected'
                    : '';

                echo "<option value=\"" .
                    htmlspecialchars($bg) .
                    "\" $selected>" .
                    htmlspecialchars($bg) .
                    "</option>";
            }

            ?>

        </select>


        <select name="urgency">

            <option value="">All Urgency</option>

            <option value="Normal"
                <?php if ($urgency === 'Normal') echo 'selected'; ?>>
                Normal
            </option>

            <option value="Urgent"
                <?php if ($urgency === 'Urgent') echo 'selected'; ?>>
                Urgent
            </option>

            <option value="Critical"
                <?php if ($urgency === 'Critical') echo 'selected'; ?>>
                Critical
            </option>

        </select>


        <select name="status">

            <option value="">All Status</option>

            <option value="Pending"
                <?php if ($status === 'Pending') echo 'selected'; ?>>
                Pending
            </option>

            <option value="Matched"
                <?php if ($status === 'Matched') echo 'selected'; ?>>
                Matched
            </option>

            <option value="Completed"
                <?php if ($status === 'Completed') echo 'selected'; ?>>
                Completed
            </option>

            <option value="Cancelled"
                <?php if ($status === 'Cancelled') echo 'selected'; ?>>
                Cancelled
            </option>

        </select>


        <button type="submit" class="btn btn-search">
            🔍 Search
        </button>


        <a href="receiver_list.php" class="btn btn-reset">
            Reset
        </a>

    </form>


    <?php if ($result && $result->num_rows > 0): ?>

        <div style="overflow-x:auto;">

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
                <th>Location</th>
                <th>Urgency</th>
                <th>Request Date</th>
                <th>Status</th>
                <th>Action</th>

            </tr>


            <?php while ($row = $result->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?php echo htmlspecialchars($row['receiver_id']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['name']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['age']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['gender']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['blood_group']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['required_organ']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['contact']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['hospital_name']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['location']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['urgency']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars(
                            $row['request_date'] ?? ''
                        ); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['status']); ?>
                    </td>


                    <!-- EDIT + DELETE -->

                    <td class="action-cell">

                        <a
                            href="receiver_edit.php?id=<?php echo $row['receiver_id']; ?>"
                            class="edit-btn"
                        >
                            Edit
                        </a>


                        <a
                            href="receiver_delete.php?id=<?php echo $row['receiver_id']; ?>"
                            class="delete-btn"
                            onclick="return confirm('Are you sure you want to delete this receiver?');"
                        >
                            Delete
                        </a>

                    </td>

                </tr>

            <?php endwhile; ?>

        </table>

        </div>

    <?php else: ?>

        <div class="no-data">
            No receivers found.
        </div>

    <?php endif; ?>

</div>

</body>

</html>

<?php

$stmt->close();
$conn->close();

?>
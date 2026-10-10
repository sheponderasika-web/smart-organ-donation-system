<?php

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
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }

        .top-buttons {
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 4px;
            color: white;
            text-decoration: none;
            margin-right: 5px;
            font-size: 14px;
        }

        .btn-back {
            background-color: #777;
        }

        .btn-add {
            background-color: #28a745;
        }

        .filter-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .filter-box form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .filter-box input,
        .filter-box select {
            padding: 9px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .search-input {
            width: 250px;
        }

        .btn-search {
            background-color: #0275d8;
            border: none;
            padding: 9px 15px;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-clear {
            background-color: #6c757d;
            padding: 9px 15px;
            color: white;
            border-radius: 4px;
            text-decoration: none;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            white-space: nowrap;
        }

        th {
            background-color: #0275d8;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .edit-btn {
            color: #0275d8;
            text-decoration: none;
            font-weight: bold;
        }

        .edit-btn:hover {
            text-decoration: underline;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #777;
        }

    </style>

</head>

<body>

<div class="container">

    <h2>Receiver List</h2>

    <div class="top-buttons">

        <a href="dashboard.php" class="btn btn-back">
            ← Back to Dashboard
        </a>

        <a href="receiver_add.php" class="btn btn-add">
            + Add Receiver
        </a>

    </div>


    <!-- Filter Section -->

    <div class="filter-box">

        <form method="GET">

            <input
                type="text"
                name="search"
                class="search-input"
                placeholder="Search name, organ, hospital, location"
                value="<?php echo htmlspecialchars($search); ?>"
            >

            <select name="blood_group">

                <option value="">All Blood Groups</option>

                <option value="A+" <?php if ($blood_group == "A+") echo "selected"; ?>>A+</option>
                <option value="A-" <?php if ($blood_group == "A-") echo "selected"; ?>>A-</option>
                <option value="B+" <?php if ($blood_group == "B+") echo "selected"; ?>>B+</option>
                <option value="B-" <?php if ($blood_group == "B-") echo "selected"; ?>>B-</option>
                <option value="O+" <?php if ($blood_group == "O+") echo "selected"; ?>>O+</option>
                <option value="O-" <?php if ($blood_group == "O-") echo "selected"; ?>>O-</option>
                <option value="AB+" <?php if ($blood_group == "AB+") echo "selected"; ?>>AB+</option>
                <option value="AB-" <?php if ($blood_group == "AB-") echo "selected"; ?>>AB-</option>

            </select>


            <select name="urgency">

                <option value="">All Urgency</option>

                <option value="Normal" <?php if ($urgency == "Normal") echo "selected"; ?>>
                    Normal
                </option>

                <option value="Urgent" <?php if ($urgency == "Urgent") echo "selected"; ?>>
                    Urgent
                </option>

                <option value="Critical" <?php if ($urgency == "Critical") echo "selected"; ?>>
                    Critical
                </option>

            </select>


            <select name="status">

                <option value="">All Status</option>

                <option value="Pending" <?php if ($status == "Pending") echo "selected"; ?>>
                    Pending
                </option>

                <option value="Matched" <?php if ($status == "Matched") echo "selected"; ?>>
                    Matched
                </option>

                <option value="Completed" <?php if ($status == "Completed") echo "selected"; ?>>
                    Completed
                </option>

                <option value="Cancelled" <?php if ($status == "Cancelled") echo "selected"; ?>>
                    Cancelled
                </option>

            </select>


            <button type="submit" class="btn-search">
                Search
            </button>

            <a href="receiver_list.php" class="btn-clear">
                Clear
            </a>

        </form>

    </div>


    <!-- Receiver Table -->

    <div class="table-wrapper">

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


            <?php if ($result->num_rows > 0) { ?>

                <?php while ($row = $result->fetch_assoc()) { ?>

                    <tr>

                        <td>
                            <?php echo $row['receiver_id']; ?>
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
                            <?php echo htmlspecialchars($row['request_date'] ?? ''); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['status']); ?>
                        </td>

                        <!-- Edit Action -->

                        <td>

                            <a
                                href="receiver_edit.php?id=<?php echo $row['receiver_id']; ?>"
                                class="edit-btn"
                            >
                                Edit
                            </a>

                        </td>

                    </tr>

                <?php } ?>

            <?php } else { ?>

                <tr>

                    <td colspan="13" class="no-data">
                        No receivers found.
                    </td>

                </tr>

            <?php } ?>

        </table>

    </div>

</div>

</body>
</html>

<?php

$stmt->close();
$conn->close();

?>
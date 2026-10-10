<?php
session_start();
include 'db.php';

// Admin session check
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Search and filter values
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$blood_group = isset($_GET['blood_group']) ? trim($_GET['blood_group']) : '';
$availability = isset($_GET['availability']) ? trim($_GET['availability']) : '';
$verification_status = isset($_GET['verification_status']) ? trim($_GET['verification_status']) : '';

$sql = "SELECT
            donor_id,
            name,
            age,
            gender,
            blood_group,
            organ,
            contact,
            location,
            availability,
            verification_status
        FROM donors
        WHERE 1=1";

$params = [];
$types = "";

// Search
if ($search !== '') {
    $sql .= " AND (name LIKE ? OR organ LIKE ? OR contact LIKE ? OR location LIKE ?)";

    $search_value = "%" . $search . "%";

    $params[] = $search_value;
    $params[] = $search_value;
    $params[] = $search_value;
    $params[] = $search_value;

    $types .= "ssss";
}

// Blood group filter
if ($blood_group !== '') {
    $sql .= " AND blood_group = ?";
    $params[] = $blood_group;
    $types .= "s";
}

// Availability filter
if ($availability !== '') {
    $sql .= " AND availability = ?";
    $params[] = $availability;
    $types .= "s";
}

// Verification filter
if ($verification_status !== '') {
    $sql .= " AND verification_status = ?";
    $params[] = $verification_status;
    $types .= "s";
}

$sql .= " ORDER BY donor_id DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database Error: " . $conn->error);
}

// Bind parameters if available
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Donor List</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 25px;
        }

        .container {
            max-width: 1300px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }

        .btn {
            display: inline-block;
            padding: 9px 13px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-back {
            background: #333;
            margin-bottom: 20px;
        }

        .btn-search {
            background: #007bff;
        }

        .btn-reset {
            background: #6c757d;
        }

        .btn-edit {
            background: #f0ad4e;
        }

        .btn-delete {
            background: #d9534f;
        }

        .search-box {
            background: #f1f3f5;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;

            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-box input,
        .search-box select {
            padding: 9px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .search-box input {
            min-width: 220px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
        }

        th {
            background: #d9534f;
            color: white;
            padding: 12px;
        }

        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        tr:hover {
            background: #f1f1f1;
        }

        .available {
            color: #198754;
            font-weight: bold;
        }

        .not-available {
            color: #dc3545;
            font-weight: bold;
        }

        .verified {
            color: #198754;
            font-weight: bold;
        }

        .pending {
            color: #f0ad4e;
            font-weight: bold;
        }

        .rejected {
            color: #dc3545;
            font-weight: bold;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #777;
        }

    </style>

</head>

<body>

<div class="container">

    <h2>🫀 Donor List</h2>

    <a href="dashboard.php" class="btn btn-back">
        ← Back to Dashboard
    </a>

    <!-- Search and Filters -->

    <form method="GET" class="search-box">

        <input
            type="text"
            name="search"
            placeholder="Search Name, Organ, Contact, Location"
            value="<?php echo htmlspecialchars($search); ?>"
        >

        <select name="blood_group">

            <option value="">All Blood Groups</option>

            <option value="A+" <?php if ($blood_group == 'A+') echo 'selected'; ?>>A+</option>
            <option value="A-" <?php if ($blood_group == 'A-') echo 'selected'; ?>>A-</option>
            <option value="B+" <?php if ($blood_group == 'B+') echo 'selected'; ?>>B+</option>
            <option value="B-" <?php if ($blood_group == 'B-') echo 'selected'; ?>>B-</option>
            <option value="O+" <?php if ($blood_group == 'O+') echo 'selected'; ?>>O+</option>
            <option value="O-" <?php if ($blood_group == 'O-') echo 'selected'; ?>>O-</option>
            <option value="AB+" <?php if ($blood_group == 'AB+') echo 'selected'; ?>>AB+</option>
            <option value="AB-" <?php if ($blood_group == 'AB-') echo 'selected'; ?>>AB-</option>

        </select>

        <select name="availability">

            <option value="">All Availability</option>

            <option value="Available" <?php if ($availability == 'Available') echo 'selected'; ?>>
                Available
            </option>

            <option value="Not Available" <?php if ($availability == 'Not Available') echo 'selected'; ?>>
                Not Available
            </option>

        </select>

        <select name="verification_status">

            <option value="">All Verification Status</option>

            <option value="Pending" <?php if ($verification_status == 'Pending') echo 'selected'; ?>>
                Pending
            </option>

            <option value="Verified" <?php if ($verification_status == 'Verified') echo 'selected'; ?>>
                Verified
            </option>

            <option value="Rejected" <?php if ($verification_status == 'Rejected') echo 'selected'; ?>>
                Rejected
            </option>

        </select>

        <button type="submit" class="btn btn-search">
            🔍 Search
        </button>

        <a href="donor_list.php" class="btn btn-reset">
            Reset
        </a>

    </form>


    <?php if ($result && $result->num_rows > 0): ?>

        <div class="table-container">

            <table>

                <tr>

                    <th>ID</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Blood Group</th>
                    <th>Organ</th>
                    <th>Contact</th>
                    <th>Location</th>
                    <th>Availability</th>
                    <th>Verification</th>
                    <th>Action</th>

                </tr>

                <?php while ($row = $result->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?php echo htmlspecialchars($row['donor_id']); ?>
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
                            <strong>
                                <?php echo htmlspecialchars($row['blood_group']); ?>
                            </strong>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['organ']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['contact']); ?>
                        </td>

                        <td>
                            📍 <?php echo htmlspecialchars($row['location']); ?>
                        </td>

                        <td>

                            <?php if ($row['availability'] === 'Available'): ?>

                                <span class="available">
                                    🟢 Available
                                </span>

                            <?php else: ?>

                                <span class="not-available">
                                    🔴 Not Available
                                </span>

                            <?php endif; ?>

                        </td>

                        <td>

                            <?php if ($row['verification_status'] === 'Verified'): ?>

                                <span class="verified">
                                    ✅ Verified
                                </span>

                            <?php elseif ($row['verification_status'] === 'Pending'): ?>

                                <span class="pending">
                                    ⏳ Pending
                                </span>

                            <?php else: ?>

                                <span class="rejected">
                                    ❌ Rejected
                                </span>

                            <?php endif; ?>

                        </td>

                        <td>

                            <a
                                href="donor_edit.php?id=<?php echo $row['donor_id']; ?>"
                                class="btn btn-edit"
                            >
                                Edit
                            </a>

                            <a
                                href="donor_delete.php?id=<?php echo $row['donor_id']; ?>"
                                class="btn btn-delete"
                                onclick="return confirm('Are you sure you want to delete this donor?');"
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
            ⚠️ No donors found.
        </div>

    <?php endif; ?>

</div>

</body>

</html>

<?php
$stmt->close();
?>
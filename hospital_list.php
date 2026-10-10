<?php

session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    echo "<h2>Access Denied! Please <a href='login.php'>Login First</a></h2>";
    exit();
}

$search = trim($_GET['search'] ?? '');

if ($search !== '') {

    $stmt = $conn->prepare(
        "SELECT * FROM hospitals
         WHERE name LIKE ?
         OR location LIKE ?
         OR contact LIKE ?
         OR email LIKE ?
         ORDER BY hospital_id DESC"
    );

    $searchTerm = "%" . $search . "%";

    $stmt->bind_param(
        "ssss",
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm
    );

} else {

    $stmt = $conn->prepare(
        "SELECT * FROM hospitals
         ORDER BY hospital_id DESC"
    );
}

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>

<head>

    <title>Hospital List</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 25px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
        }

        h2 {
            text-align: center;
            color: #3498DB;
            margin-bottom: 20px;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 10px;
        }

        .btn {
            display: inline-block;
            padding: 9px 14px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-weight: bold;
        }

        .btn-back {
            background: #333;
        }

        .btn-add {
            background: #27AE60;
        }

        .search-box {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .search-box input {
            width: 70%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .search-box button {
            padding: 10px 18px;
            border: none;
            background: #3498DB;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }

        th {
            background: #3498DB;
            color: white;
            padding: 12px;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        tr:hover {
            background: #f9f9f9;
        }

        .edit-btn {
            background: #F39C12;
            color: white;
            padding: 6px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }

        .delete-btn {
            background: #E74C3C;
            color: white;
            padding: 6px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }

        .empty {
            background: white;
            padding: 25px;
            text-align: center;
            border-radius: 8px;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="top-bar">

        <a class="btn btn-back" href="dashboard.php">
            ← Dashboard
        </a>

        <a class="btn btn-add" href="hospital_add.php">
            ➕ Add Hospital
        </a>

    </div>


    <h2>🏥 Hospital List</h2>


    <div class="search-box">

        <form method="GET">

            <input
                type="text"
                name="search"
                placeholder="Search hospital, location, contact or email..."
                value="<?php echo htmlspecialchars($search); ?>"
            >

            <button type="submit">
                🔍 Search
            </button>

        </form>

    </div>


    <?php if ($result->num_rows > 0) { ?>

        <table>

            <tr>

                <th>ID</th>
                <th>Hospital Name</th>
                <th>Location</th>
                <th>Contact</th>
                <th>Email</th>
                <th>Action</th>

            </tr>


            <?php while ($row = $result->fetch_assoc()) { ?>

                <tr>

                    <td>
                        <?php echo $row['hospital_id']; ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['name']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['location']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['contact']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['email']); ?>
                    </td>

                    <td>

                        <a
                            class="edit-btn"
                            href="hospital_edit.php?id=<?php echo $row['hospital_id']; ?>"
                        >
                            ✏️ Edit
                        </a>

                        <a
                            class="delete-btn"
                            href="hospital_delete.php?id=<?php echo $row['hospital_id']; ?>"
                            onclick="return confirm('Are you sure you want to delete this hospital?');"
                        >
                            🗑️ Delete
                        </a>

                    </td>

                </tr>

            <?php } ?>

        </table>

    <?php } else { ?>

        <div class="empty">

            <h3>No hospitals found.</h3>

            <p>Please add a hospital first.</p>

        </div>

    <?php } ?>

</div>

</body>

</html>

<?php

$stmt->close();
$conn->close();

?>
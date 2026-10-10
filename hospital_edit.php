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
    "SELECT * FROM hospitals WHERE hospital_id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Hospital not found.");
}

$hospital = $result->fetch_assoc();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name']);
    $location = trim($_POST['location']);
    $contact = trim($_POST['contact']);
    $email = trim($_POST['email']);

    if ($name === "" || $location === "" || $contact === "") {

        $error = "Please fill in all required fields.";

    } elseif (!preg_match('/^[0-9+\-\s()]{7,20}$/', $contact)) {

        $error = "Please enter a valid contact number.";

    } elseif ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } else {

        $stmt = $conn->prepare(
            "UPDATE hospitals SET
                name = ?,
                location = ?,
                contact = ?,
                email = ?
             WHERE hospital_id = ?"
        );

        $stmt->bind_param(
            "ssssi",
            $name,
            $location,
            $contact,
            $email,
            $id
        );

        if ($stmt->execute()) {

            header("Location: hospital_list.php");
            exit();

        } else {

            $error = "Error updating hospital: " . $stmt->error;
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Edit Hospital</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #3498DB;
            margin-top: 0;
        }

        .back {
            display: inline-block;
            background: #333;
            color: white;
            padding: 8px 14px;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #3498DB;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }

    </style>

</head>

<body>

<div class="container">

    <a class="back" href="hospital_list.php">
        ← Back to Hospital List
    </a>

    <h2>✏️ Edit Hospital</h2>

    <?php if ($error !== "") { ?>

        <div class="error">
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php } ?>

    <form method="POST">

        <div class="form-group">

            <label>Hospital Name *</label>

            <input
                type="text"
                name="name"
                required
                value="<?php echo htmlspecialchars($hospital['name']); ?>"
            >

        </div>


        <div class="form-group">

            <label>City / Location *</label>

            <input
                type="text"
                name="location"
                required
                value="<?php echo htmlspecialchars($hospital['location']); ?>"
            >

        </div>


        <div class="form-group">

            <label>Contact Number *</label>

            <input
                type="text"
                name="contact"
                required
                value="<?php echo htmlspecialchars($hospital['contact']); ?>"
            >

        </div>


        <div class="form-group">

            <label>Email Address</label>

            <input
                type="email"
                name="email"
                value="<?php echo htmlspecialchars($hospital['email']); ?>"
            >

        </div>


        <button type="submit">
            💾 Update Hospital
        </button>

    </form>

</div>

</body>

</html>
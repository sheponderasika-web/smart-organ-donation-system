<?php

session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    echo "<h2>Access Denied! Please <a href='login.php'>Login First</a></h2>";
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name']);
    $location = trim($_POST['location']);
    $contact = trim($_POST['contact']);
    $email = trim($_POST['email']);

    // Validation

    if ($name === "" || $location === "" || $contact === "") {

        $message = "<div class='alert danger'>
            ⚠️ Please fill in all required fields.
        </div>";

    } elseif (!preg_match('/^[0-9+\-\s()]{7,20}$/', $contact)) {

        $message = "<div class='alert danger'>
            ⚠️ Please enter a valid contact number.
        </div>";

    } elseif ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "<div class='alert danger'>
            ⚠️ Please enter a valid email address.
        </div>";

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO hospitals
            (name, location, contact, email)
            VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssss",
            $name,
            $location,
            $contact,
            $email
        );

        if ($stmt->execute()) {

            $message = "<div class='alert success'>
                ✅ Hospital registered successfully!
            </div>";

        } else {

            $message = "<div class='alert danger'>
                ❌ Failed to register hospital.
            </div>";
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Add Hospital</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
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
            margin-bottom: 25px;
        }

        .btn-back {
            display: inline-block;
            background-color: #333;
            color: white;
            padding: 8px 14px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .btn-submit {
            width: 100%;
            background-color: #3498DB;
            color: white;
            border: none;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-submit:hover {
            background-color: #2980B9;
        }

        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

    </style>

</head>

<body>

<div class="container">

    <a class="btn-back" href="dashboard.php">
        ← Back to Dashboard
    </a>

    <h2>🏥 Register New Hospital</h2>

    <?php echo $message; ?>


    <form method="POST" action="">

        <div class="form-group">

            <label>Hospital Name *</label>

            <input
                type="text"
                name="name"
                required
                placeholder="e.g. KEM Hospital"
            >

        </div>


        <div class="form-group">

            <label>City / Location *</label>

            <input
                type="text"
                name="location"
                required
                placeholder="e.g. Mumbai"
            >

        </div>


        <div class="form-group">

            <label>Contact Number *</label>

            <input
                type="tel"
                name="contact"
                required
                placeholder="e.g. 022-24107000"
            >

        </div>


        <div class="form-group">

            <label>Email Address</label>

            <input
                type="email"
                name="email"
                placeholder="e.g. info@hospital.org"
            >

        </div>


        <button
            type="submit"
            class="btn-submit"
        >
            ➕ Add Hospital
        </button>

    </form>

</div>

</body>

</html>
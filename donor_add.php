<?php
session_start();
include 'db.php';

// Admin login check
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get form data
    $name = trim($_POST['name']);
    $age = (int) $_POST['age'];
    $gender = $_POST['gender'];
    $blood_group = $_POST['blood_group'];
    $organ = $_POST['organ'];
    $contact = trim($_POST['contact']);
    $location = trim($_POST['location']);
    $availability = $_POST['availability'];
    $verification_status = $_POST['verification_status'];

    // Basic validation
    if ($age < 18 || $age > 100) {
        $error = "Please enter a valid donor age between 18 and 100.";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $contact)) {
        $error = "Please enter a valid contact number.";
    } else {

        // Secure insert using prepared statement
        $stmt = $conn->prepare(
            "INSERT INTO donors
            (name, age, gender, blood_group, organ, contact, location, availability, verification_status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        if ($stmt) {

            $stmt->bind_param(
                "sisssssss",
                $name,
                $age,
                $gender,
                $blood_group,
                $organ,
                $contact,
                $location,
                $availability,
                $verification_status
            );

            if ($stmt->execute()) {
                $success = "Donor added successfully!";
            } else {
                $error = "Error adding donor: " . $stmt->error;
            }

            $stmt->close();

        } else {
            $error = "Database error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Donor</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }

        .back {
            display: inline-block;
            text-decoration: none;
            background: #333;
            color: white;
            padding: 9px 14px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 17px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
            color: #444;
        }

        input,
        select {
            width: 100%;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        input:focus,
        select:focus {
            border-color: #007bff;
            outline: none;
        }

        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            background: #d9534f;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        .btn:hover {
            background: #c9302c;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

<div class="container">

    <a href="dashboard.php" class="back">← Back to Dashboard</a>

    <h2>🫀 Add New Donor</h2>

    <?php if (!empty($success)): ?>
        <div class="success">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <div class="form-group">
            <label>Full Name</label>
            <input
                type="text"
                name="name"
                maxlength="100"
                required
            >
        </div>

        <div class="form-group">
            <label>Age</label>
            <input
                type="number"
                name="age"
                min="18"
                max="100"
                required
            >
        </div>

        <div class="form-group">
            <label>Gender</label>
            <select name="gender" required>
                <option value="">-- Select Gender --</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <div class="form-group">
            <label>Blood Group</label>
            <select name="blood_group" required>
                <option value="">-- Select Blood Group --</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
            </select>
        </div>

        <div class="form-group">
            <label>Organ</label>
            <select name="organ" required>
                <option value="">-- Select Organ --</option>
                <option value="Kidney">Kidney</option>
                <option value="Liver">Liver</option>
                <option value="Heart">Heart</option>
                <option value="Lung">Lung</option>
                <option value="Pancreas">Pancreas</option>
                <option value="Cornea">Cornea</option>
            </select>
        </div>

        <div class="form-group">
            <label>Contact Number</label>
            <input
                type="text"
                name="contact"
                maxlength="15"
                placeholder="Enter contact number"
                required
            >
        </div>

        <div class="form-group">
            <label>Location</label>
            <input
                type="text"
                name="location"
                maxlength="100"
                placeholder="e.g. Mumbai"
                required
            >
        </div>

        <div class="form-group">
            <label>Availability</label>
            <select name="availability" required>
                <option value="Available">Available</option>
                <option value="Not Available">Not Available</option>
            </select>
        </div>

        <div class="form-group">
            <label>Verification Status</label>
            <select name="verification_status" required>
                <option value="Pending">Pending</option>
                <option value="Verified">Verified</option>
                <option value="Rejected">Rejected</option>
            </select>
        </div>

        <button type="submit" class="btn">
            Add Donor
        </button>

    </form>

</div>

</body>
</html>
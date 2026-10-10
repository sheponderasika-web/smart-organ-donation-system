<?php

session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    echo "<h2>Access Denied! Please <a href='login.php'>Login First</a></h2>";
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $age = (int) $_POST['age'];
    $gender = $_POST['gender'];
    $blood_group = $_POST['blood_group'];
    $required_organ = $_POST['required_organ'];
    $contact = trim($_POST['contact']);
    $hospital_name = trim($_POST['hospital_name']);
    $location = trim($_POST['location']);
    $urgency = $_POST['urgency'];
    $request_date = $_POST['request_date'];
    $status = $_POST['status'];

    if ($age < 1 || $age > 120) {

        $message = "<p style='color:red;'>Age must be between 1 and 120.</p>";

    } elseif (!preg_match('/^[0-9]{10,15}$/', $contact)) {

        $message = "<p style='color:red;'>Contact number must contain 10 to 15 digits.</p>";

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO receivers
            (name, age, gender, blood_group, required_organ, contact,
             hospital_name, location, urgency, request_date, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "sisssssssss",
            $name,
            $age,
            $gender,
            $blood_group,
            $required_organ,
            $contact,
            $hospital_name,
            $location,
            $urgency,
            $request_date,
            $status
        );

        if ($stmt->execute()) {
            $message = "<p style='color:green;'>Receiver Added Successfully!</p>";
        } else {
            $message = "<p style='color:red;'>Error: " . htmlspecialchars($stmt->error) . "</p>";
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html>
<head>

    <title>Add Receiver</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 30px;
        }

        .form-card {
            max-width: 550px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            padding: 9px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-submit {
            background-color: #0275d8;
            width: 100%;
            margin-top: 10px;
        }

        .btn-cancel {
            background-color: #777;
            margin-bottom: 15px;
        }

    </style>

</head>

<body>

<div class="form-card">

    <a href="dashboard.php" class="btn btn-cancel">
        ← Back to Dashboard
    </a>

    <h2>Add New Receiver</h2>

    <?php echo $message; ?>

    <form method="POST" action="">

        <div class="form-group">
            <label>Full Name:</label>
            <input type="text" name="name" required>
        </div>

        <div class="form-group">
            <label>Age:</label>
            <input type="number" name="age" min="1" max="120" required>
        </div>

        <div class="form-group">
            <label>Gender:</label>

            <select name="gender" required>
                <option value="">-- Select --</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>

        </div>

        <div class="form-group">
            <label>Blood Group:</label>

            <select name="blood_group" required>
                <option value="">-- Select --</option>
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
            <label>Required Organ:</label>

            <select name="required_organ" required>
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
            <label>Contact Number:</label>
            <input type="text" name="contact" maxlength="15" required>
        </div>

        <div class="form-group">
            <label>Hospital Name:</label>
            <input type="text" name="hospital_name" required>
        </div>

        <div class="form-group">
            <label>Location:</label>
            <input type="text" name="location" placeholder="e.g. Mumbai" required>
        </div>

        <div class="form-group">
            <label>Urgency:</label>

            <select name="urgency" required>
                <option value="Normal">Normal</option>
                <option value="Urgent">Urgent</option>
                <option value="Critical">Critical</option>
            </select>

        </div>

        <div class="form-group">
            <label>Request Date:</label>

            <input
                type="date"
                name="request_date"
                value="<?php echo date('Y-m-d'); ?>"
                required
            >

        </div>

        <div class="form-group">
            <label>Status:</label>

            <select name="status" required>
                <option value="Pending">Pending</option>
                <option value="Matched">Matched</option>
                <option value="Completed">Completed</option>
                <option value="Cancelled">Cancelled</option>
            </select>

        </div>

        <button type="submit" class="btn btn-submit">
            Add Receiver
        </button>

    </form>

</div>

</body>
</html>
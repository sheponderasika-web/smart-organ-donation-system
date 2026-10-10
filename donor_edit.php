<?php

session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid donor ID.");
}

$id = (int) $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM donors WHERE donor_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Donor not found.");
}

$donor = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $age = (int) $_POST['age'];
    $gender = $_POST['gender'];
    $blood_group = $_POST['blood_group'];
    $organ = $_POST['organ'];
    $contact = trim($_POST['contact']);
    $location = trim($_POST['location']);
    $availability = $_POST['availability'];
    $verification_status = $_POST['verification_status'];

    if ($age < 18 || $age > 100) {
        $error = "Age must be between 18 and 100.";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $contact)) {
        $error = "Contact number must contain 10 to 15 digits.";
    } else {

        $stmt = $conn->prepare(
            "UPDATE donors SET
                name = ?,
                age = ?,
                gender = ?,
                blood_group = ?,
                organ = ?,
                contact = ?,
                location = ?,
                availability = ?,
                verification_status = ?
             WHERE donor_id = ?"
        );

        $stmt->bind_param(
            "sisssssssi",
            $name,
            $age,
            $gender,
            $blood_group,
            $organ,
            $contact,
            $location,
            $availability,
            $verification_status,
            $id
        );

        if ($stmt->execute()) {
            header("Location: donor_list.php");
            exit();
        } else {
            $error = "Error updating donor: " . $stmt->error;
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Donor</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            padding: 30px;
        }

        .container {
            width: 500px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }

        h2 {
            text-align: center;
        }

        label {
            display: block;
            margin-top: 12px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 9px;
            margin-top: 5px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        .back {
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="back">
        <a href="donor_list.php">← Back to Donor List</a>
    </div>

    <h2>Edit Donor</h2>

    <?php if (isset($error)): ?>
        <div class="error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label>Name</label>
        <input type="text" name="name"
               value="<?php echo htmlspecialchars($donor['name']); ?>"
               required>

        <label>Age</label>
        <input type="number" name="age"
               value="<?php echo htmlspecialchars($donor['age']); ?>"
               min="18" max="100" required>

        <label>Gender</label>
        <select name="gender" required>
            <option value="Male" <?php if ($donor['gender'] == 'Male') echo 'selected'; ?>>Male</option>
            <option value="Female" <?php if ($donor['gender'] == 'Female') echo 'selected'; ?>>Female</option>
            <option value="Other" <?php if ($donor['gender'] == 'Other') echo 'selected'; ?>>Other</option>
        </select>

        <label>Blood Group</label>
        <select name="blood_group" required>
            <?php
            $blood_groups = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];

            foreach ($blood_groups as $bg) {
                $selected = ($donor['blood_group'] == $bg) ? 'selected' : '';
                echo "<option value=\"$bg\" $selected>$bg</option>";
            }
            ?>
        </select>

        <label>Organ</label>
        <select name="organ" required>
            <?php
            $organs = ['Kidney', 'Liver', 'Heart', 'Lung', 'Pancreas', 'Cornea'];

            foreach ($organs as $org) {
                $selected = ($donor['organ'] == $org) ? 'selected' : '';
                echo "<option value=\"$org\" $selected>$org</option>";
            }
            ?>
        </select>

        <label>Contact</label>
        <input type="text" name="contact"
               value="<?php echo htmlspecialchars($donor['contact']); ?>"
               required>

        <label>Location</label>
        <input type="text" name="location"
               value="<?php echo htmlspecialchars($donor['location']); ?>"
               required>

        <label>Availability</label>
        <select name="availability" required>
            <option value="Available"
                <?php if ($donor['availability'] == 'Available') echo 'selected'; ?>>
                Available
            </option>

            <option value="Not Available"
                <?php if ($donor['availability'] == 'Not Available') echo 'selected'; ?>>
                Not Available
            </option>
        </select>

        <label>Verification Status</label>
        <select name="verification_status" required>
            <option value="Pending"
                <?php if ($donor['verification_status'] == 'Pending') echo 'selected'; ?>>
                Pending
            </option>

            <option value="Verified"
                <?php if ($donor['verification_status'] == 'Verified') echo 'selected'; ?>>
                Verified
            </option>

            <option value="Rejected"
                <?php if ($donor['verification_status'] == 'Rejected') echo 'selected'; ?>>
                Rejected
            </option>
        </select>

        <button type="submit">Update Donor</button>

    </form>

</div>

</body>
</html>